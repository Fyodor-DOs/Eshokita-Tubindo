<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $customerModel;
    protected $pengirimanModel;
    protected $invoiceModel;
    protected $db;

    public function __construct()
    {
        $this->customerModel   = new \App\Models\CustomerModel();
        $this->pengirimanModel = new \App\Models\PengirimanModel();
        $this->invoiceModel    = new \App\Models\InvoiceModel();
        $this->db              = \Config\Database::connect();
    }

    public function index()
    {
        // Enforce authentication at controller level to guarantee
        // unauthenticated visitors are redirected to the login page
        // even if route filters are misconfigured.
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $month = $this->request->getGet('month') ?: date('Y-m'); // format YYYY-MM
        // Get counts within the selected month
        [$year, $mon] = explode('-', $month);

        $pengirimanCount = $this->pengirimanModel
            ->where('YEAR(tanggal)', (int)$year)
            ->where('MONTH(tanggal)', (int)$mon)
            ->countAllResults();

        $rekapData = $this->getRekapPenjualan((int)$year, (int)$mon);

        $data = [
            'customer'   => $this->customerModel->countAll(),
            'pengiriman' => $pengirimanCount,
            'pembayaran' => $this->getPembayaran((int)$year, (int)$mon),
            'month'      => $month,
            'rekap'      => $rekapData['rows'],
            'rekap_summary' => $rekapData['summary'],
            'rekap_totals'  => $rekapData['totals'],
            'rekap_variants' => $rekapData['variants'],
        ];

        return view('pages/dashboard/index', $data);
    }

    private function getPembayaran(int $year, int $mon)
    {
        $result = $this->pengirimanModel
            ->select('pembayaran, COUNT(*) as total')
            ->where('YEAR(tanggal)', $year)
            ->where('MONTH(tanggal)', $mon)
            ->groupBy('pembayaran')
            ->findAll();

        return [
            'label' => array_column($result, 'pembayaran'),
            'series' => array_column($result, 'total')
        ];
    }

    private function getRekapPenjualan(int $year, int $mon): array
    {
        // Ambil semua invoice pada bulan (issue_date) dan transaksi terkait
        $builder = $this->db->table('invoice i')
            ->select('i.id_invoice, i.invoice_no, i.issue_date, i.amount as invoice_amount, c.nama as customer_name, tr.items as tr_items, pg.pembayaran as pg_pembayaran, r.nama_wilayah as rute_name')
            ->join('transaction tr', 'tr.id_transaction = i.id_transaction', 'left')
            ->join('customer c', 'c.id_customer = tr.id_customer', 'left')
            ->join('pengiriman pg', 'pg.id_pengiriman = i.id_pengiriman', 'left')
            ->join('rute r', 'r.kode_rute = c.kode_rute', 'left')
            ->where('YEAR(i.issue_date)', $year)
            ->where('MONTH(i.issue_date)', $mon)
            ->orderBy('c.nama', 'ASC');

        $invoices = $builder->get()->getResultArray();

        // Preload payments per invoice
        $payRows = $this->db->table('payment')
            ->select('id_invoice, method, SUM(amount) as total_method')
            ->whereIn('id_invoice', array_column($invoices, 'id_invoice') ?: [0])
            ->groupBy('id_invoice, method')
            ->get()->getResultArray();
        $paymentsGrouped = [];
        foreach ($payRows as $p) {
            $paymentsGrouped[$p['id_invoice']][$p['method']] = (float)$p['total_method'];
        }

        $rowsCash = [];
        $rowsKredit = [];

        // Get all products with their details (category, unit/weight)
        $products = $this->db->table('product p')
            ->select('p.id_product, p.name, p.unit, p.qty, pc.name as category_name')
            ->join('product_category pc', 'pc.id_category = p.id_category')
            ->whereIn('pc.name', ['Kristal Besar', 'Kristal Kecil', 'Serut'])
            ->orderBy('pc.name', 'ASC')
            ->orderBy('p.unit', 'ASC')
            ->get()->getResultArray();
        
        // Build product mapping by id for quick lookup
        $productMap = [];
        foreach ($products as $p) {
            $productMap[$p['id_product']] = $p;
        }

        // Summary buckets - dynamic based on actual products
        $summary = [];
        foreach ($products as $p) {
            $key = $p['category_name'] . ' ' . $p['unit'] . 'kg';
            $summary[$key] = ['sisa' => (int)$p['qty'], 'laku' => 0];
        }

        $totals = [
            'cash' => 0.0,
            'kredit' => 0.0,
            'grand' => 0.0,
            'total_hrg' => 0.0,
        ];

        foreach ($invoices as $inv) {
            $items = [];
            if (!empty($inv['tr_items'])) {
                $decoded = json_decode($inv['tr_items'], true);
                if (is_array($decoded)) $items = $decoded;
            }

            $kb = 0; $kk = 0; $srt = 0; $hrg = (float)$inv['invoice_amount'];
            $variants = []; // Store qty by variant key (e.g., "KB10", "KK20", "SRT10")
            $totalQty = 0;

            foreach ($items as $it) {
                $qty  = (int)($it['qty'] ?? 0);
                $idProduct = (int)($it['id_product'] ?? 0);
                
                if ($idProduct > 0 && isset($productMap[$idProduct])) {
                    $product = $productMap[$idProduct];
                    $categoryName = strtolower($product['category_name']);
                    $summaryKey = $product['category_name'] . ' ' . $product['unit'] . 'kg';
                    $unit = $product['unit'];
                    
                    // Create variant key based on category and unit
                    $variantKey = '';
                    if (strpos($categoryName, 'kristal besar') !== false) {
                        $variantKey = 'KB' . $unit;
                        $kb += $qty;
                    } elseif (strpos($categoryName, 'kristal kecil') !== false) {
                        $variantKey = 'KK' . $unit;
                        $kk += $qty;
                    } elseif (strpos($categoryName, 'serut') !== false) {
                        $variantKey = 'SRT' . $unit;
                        $srt += $qty;
                    }
                    
                    if ($variantKey) {
                        $variants[$variantKey] = ($variants[$variantKey] ?? 0) + $qty;
                    }
                    
                    $totalQty += $qty;
                    
                    // Add to summary laku
                    if (isset($summary[$summaryKey])) {
                        $summary[$summaryKey]['laku'] += $qty;
                    }
                }
            }

            // Determine payment classification
            $cashPaid   = $paymentsGrouped[$inv['id_invoice']]['cash']   ?? 0.0;
            $kreditPaid = $paymentsGrouped[$inv['id_invoice']]['kredit'] ?? 0.0;
            // If no payment yet, fall back to pengiriman.pembayaran flag
            if ($cashPaid == 0 && $kreditPaid == 0) {
                $flag = strtolower($inv['pg_pembayaran'] ?? '');
                if ($flag === 'cash') { $cashPaid = $hrg; }
                elseif ($flag === 'kredit') { $kreditPaid = $hrg; }
            }

            $totals['cash']   += $cashPaid;
            $totals['kredit'] += $kreditPaid;
            $totals['grand']  += $hrg;
            $totals['total_hrg'] += $hrg;

            $row = [
                'nota'    => $inv['invoice_no'],
                'customer'=> $inv['customer_name'] ?? '-',
                'rute'    => $inv['rute_name'] ?? '-',
                'kb'      => $kb ?: '',
                'kk'      => $kk ?: '',
                'srt'     => $srt ?: '',
                'variants'=> $variants,
                'jumlah'  => $totalQty,
                'hrg'     => number_format($hrg,0,',','.'),
                'cash'    => $cashPaid ? number_format($cashPaid,0,',','.') : '',
                'kredit'  => $kreditPaid ? number_format($kreditPaid,0,',','.') : '',
                'ket'     => '',
            ];

            // Categorize row: if any kredit amount then kredit else cash
            if ($kreditPaid > 0 && $cashPaid == 0) {
                $rowsKredit[] = $row;
            } else {
                $rowsCash[] = $row; // mixed or pure cash are listed under cash for now
            }
        }

        // Get all unique variants for table headers
        $variantsList = [];
        foreach ($products as $p) {
            $categoryName = strtolower($p['category_name']);
            $unit = $p['unit'];
            if (strpos($categoryName, 'kristal besar') !== false) {
                $variantsList[] = ['key' => 'KB' . $unit, 'label' => 'KB' . $unit . 'kg'];
            } elseif (strpos($categoryName, 'kristal kecil') !== false) {
                $variantsList[] = ['key' => 'KK' . $unit, 'label' => 'KK' . $unit . 'kg'];
            } elseif (strpos($categoryName, 'serut') !== false) {
                $variantsList[] = ['key' => 'SRT' . $unit, 'label' => 'SRT' . $unit . 'kg'];
            }
        }

        return [
            'rows' => [ 'cash' => $rowsCash, 'kredit' => $rowsKredit ],
            'summary' => $summary,
            'totals' => $totals,
            'variants' => $variantsList,
        ];
    }

    public function rekap()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $date  = $this->request->getGet('date') ?: date('Y-m-d');
        $sopir = $this->request->getGet('sopir') ?: '';
        $kenek = $this->request->getGet('kenek') ?: '';

        $data = [
            'date' => $date,
            'sopir' => $sopir,
            'kenek' => $kenek,
        ];

        return view('pages/rekap/index', $data);
    }

    public function rekapExport()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $date  = $this->request->getGet('date') ?: date('Y-m-d');
        // Accept YYYY-MM or full date; extract year & month
        $ym = substr($date,0,7); // YYYY-MM
        [$year,$mon] = explode('-', $ym);
        $rekapData = $this->getRekapPenjualan((int)$year,(int)$mon);
        $data = [
            'date' => $date,
            'rows' => $rekapData['rows'],
            'summary' => $rekapData['summary'],
            'totals' => $rekapData['totals'],
            'variants' => $rekapData['variants'],
        ];

        $html = view('pages/rekap/pdf', $data);

        // Generate PDF
        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Rekap_Penjualan_' . date('Ymd', strtotime($date)) . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
}
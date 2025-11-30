<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SuratJalanController extends BaseController
{
    protected $suratJalanModel;
    protected $ruteModel;
    protected $invoiceModel;

    public function __construct()
    {
        $this->suratJalanModel = new \App\Models\SuratJalanModel();
        $this->ruteModel = new \App\Models\RuteModel();
        $this->invoiceModel = new \App\Models\InvoiceModel();
    }

    public function index()
    {
        $data['suratJalan'] = $this->suratJalanModel
            ->select('nota.*, rute.nama_wilayah as rute_name, customer.nama as customer_name, pengiriman.no_bon, pengiriman.id_pengiriman')
            ->join('rute', 'nota.kode_rute = rute.kode_rute', 'left')
            ->join('customer', 'customer.id_customer = nota.id_customer', 'left')
            ->join('pengiriman', 'pengiriman.id_pengiriman = nota.id_pengiriman', 'left')
            ->orderBy('nota.id_surat_jalan', 'DESC')
            ->findAll();

        return view('pages/nota/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() == 'POST') {
            $idPengiriman = (int)$this->request->getPost('id_pengiriman');
            $tanggal      = $this->request->getPost('tanggal') ?: date('Y-m-d');

            $db = \Config\Database::connect();
            $pengirimanModel = new \App\Models\PengirimanModel();
            $pengiriman = $pengirimanModel->find($idPengiriman);
            if (!$pengiriman) {
                return $this->response->setJSON(['success' => false, 'message' => 'Pengiriman tidak ditemukan']);
            }

            // Ambil semua invoice yang terkait dengan pengiriman ini
            $invoices = $db->table('invoice i')
                ->select('i.*, t.id_customer, t.items, c.nama as customer_name')
                ->join('transaction t', 'i.id_transaction = t.id_transaction', 'left')
                ->join('customer c', 't.id_customer = c.id_customer', 'left')
                ->where('i.id_pengiriman', $idPengiriman)
                ->get()->getResultArray();

            if (empty($invoices)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada invoice/customer untuk BON ini']);
            }

            // Group invoices by customer
            $byCustomer = [];
            foreach ($invoices as $inv) {
                $cid = (int)$inv['id_customer'];
                if (!isset($byCustomer[$cid])) {
                    $byCustomer[$cid] = [
                        'id_customer' => $cid,
                        'customer_name' => $inv['customer_name'],
                        'invoices' => [],
                        'items' => []
                    ];
                }
                $byCustomer[$cid]['invoices'][] = $inv;
                // Collect items from transaction
                if (!empty($inv['items'])) {
                    $decoded = json_decode($inv['items'], true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $it) {
                            $pid = (int)($it['id_product'] ?? 0);
                            $qty = (int)($it['qty'] ?? 0);
                            $harga = (float)($it['price'] ?? 0);
                            if ($pid > 0 && $qty > 0) {
                                // Aggregate same products
                                $found = false;
                                foreach ($byCustomer[$cid]['items'] as &$existing) {
                                    if ($existing['id_product'] == $pid && $existing['harga'] == $harga) {
                                        $existing['qty'] += $qty;
                                        $existing['total'] = $existing['qty'] * $existing['harga'];
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) {
                                    $byCustomer[$cid]['items'][] = [
                                        'id_product' => $pid,
                                        'qty' => $qty,
                                        'harga' => $harga,
                                        'total' => $qty * $harga,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Create surat jalan for each customer
            $db->transStart();
            $createdCount = 0;
            foreach ($byCustomer as $cid => $data) {
                $input = [
                    'tanggal'       => $tanggal,
                    'kode_rute'     => $pengiriman['kode_rute'],
                    'muatan'        => json_encode($data['items']),
                    'ttd_produksi'  => null,
                    'id_pengiriman' => $idPengiriman,
                    'id_customer'   => $cid,
                    'nama_penerima' => null,
                    'ttd_penerima'  => null,
                ];

                if ($this->suratJalanModel->insert($input)) {
                    $createdCount++;
                }
            }
            $db->transComplete();

            if ($db->transStatus() === false || $createdCount == 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat surat jalan']);
            }

            // Add tracking
            try {
                $tracking = new \App\Models\ShipmentTrackingModel();
                $tracking->insert([
                    'id_pengiriman' => $idPengiriman,
                    'status'        => 'created',
                    'location'      => null,
                    'note'          => "Surat jalan dibuat untuk {$createdCount} customer",
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $th) { /* ignore */ }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil membuat {$createdCount} surat jalan untuk {$createdCount} customer berbeda",
                'url'     => '/surat-jalan',
            ]);
        }

        // GET: Ambil daftar pengiriman yang belum dibuat surat jalannya
        $db = \Config\Database::connect();
        $pengirimanModel = new \App\Models\PengirimanModel();
        
        // Get all pengiriman
        $allPengiriman = $pengirimanModel
            ->select('id_pengiriman, no_bon, kode_rute, tanggal')
            ->orderBy('id_pengiriman', 'DESC')
            ->findAll();
        
        // Filter out those that already have surat jalan
        $pengirimanList = [];
        foreach ($allPengiriman as $p) {
            $hasSJ = $db->table('nota')
                ->where('id_pengiriman', $p['id_pengiriman'])
                ->countAllResults();
            if ($hasSJ == 0) {
                $pengirimanList[] = $p;
            }
        }
        
        $data['pengirimanList'] = $pengirimanList;
        return view('pages/nota/create', $data);
    }

    public function createQuick($idPengiriman)
    {
        $pengirimanModel = new \App\Models\PengirimanModel();
        $pengiriman = $pengirimanModel->find((int)$idPengiriman);
        if (!$pengiriman) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengiriman tidak ditemukan']);
        }

        $idCustomer = (int)($pengiriman['id_customer'] ?? 0);
        if ($idCustomer <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer belum terikat pada pengiriman ini']);
        }

        $db = \Config\Database::connect();
        $trx = $db->query(
            "SELECT t.* FROM transaction t WHERE t.id_customer = ? ORDER BY t.transaction_date DESC, t.id_transaction DESC LIMIT 1",
            [$idCustomer]
        )->getRowArray();

        $items = [];
        if ($trx && !empty($trx['items'])) {
            $decoded = json_decode($trx['items'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $it) {
                    $pid = (int)($it['id_product'] ?? 0);
                    $qty = (int)($it['qty'] ?? 0);
                    $harga = (float)($it['price'] ?? 0);
                    if ($pid > 0 && $qty > 0) {
                        $items[] = [
                            'id_product' => $pid,
                            'qty'        => $qty,
                            'harga'      => $harga,
                            'total'      => $qty * $harga,
                        ];
                    }
                }
            }
        }

        $input = [
            'tanggal'       => date('Y-m-d'),
            'kode_rute'     => $pengiriman['kode_rute'] ?? '',
            'muatan'        => json_encode($items ?: []),
            'ttd_produksi'  => null,
            'id_pengiriman' => (int)$idPengiriman,
            'id_customer'   => $idCustomer,
            'nama_penerima' => $pengiriman['nama_penerima'] ?? null,
            'ttd_penerima'  => $pengiriman['ttd_penerima'] ?? null,
        ];

        if ($this->suratJalanModel->insert($input)) {
            $idSurat = (int)$this->suratJalanModel->getInsertID();

            // Tidak mengurangi stok di tahap Surat Jalan (quick) – sudah ditangani saat order.
            try { log_message('error', 'SJ quick: skip stock deduction (handled at order time).'); } catch (\Throwable $th) {}

            try {
                $tracking = new \App\Models\ShipmentTrackingModel();
                $tracking->insert([
                    'id_pengiriman' => (int)$idPengiriman,
                    'status'        => 'created',
                    'location'      => null,
                    'note'          => 'Surat jalan dibuat (quick)',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $th) { /* ignore */ }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat Jalan berhasil dibuat dari Pengiriman',
                'url'     => base_url('surat-jalan'),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $this->suratJalanModel->errors(),
        ]);
    }

    public function detail($id)
    {
        $sj = $this->suratJalanModel
            ->select('nota.*, rute.nama_wilayah as rute_name, customer.nama as customer_name, customer.alamat as customer_address')
            ->join('rute', 'nota.kode_rute = rute.kode_rute', 'left')
            ->join('customer', 'nota.id_customer = customer.id_customer', 'left')
            ->find($id);
        if(!$sj){ throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat Jalan tidak ditemukan'); }

        // Decode muatan -> items untuk tampilan rinci
        $items = [];
        $muatan = [];
        if (!empty($sj['muatan'])) {
            $decoded = json_decode($sj['muatan'], true);
            if (is_array($decoded)) { $muatan = $decoded; }
        }
        if ($muatan) {
            $productModel = new \App\Models\ProductModel();
            $prodCache = [];
            foreach ($muatan as $row) {
                $pid = (int)($row['id_product'] ?? 0);
                $qty = (float)($row['qty'] ?? 0);
                if ($pid <= 0 || $qty <= 0) continue;
                if (!isset($prodCache[$pid])) { $prodCache[$pid] = $productModel->find($pid) ?: []; }
                $sku = $prodCache[$pid]['sku'] ?? ('#'.$pid);
                $name = $prodCache[$pid]['name'] ?? ('Produk #'.$pid);
                // Berat mengikuti unit atau parse dari SKU
                $berat = null; $unitVal = $prodCache[$pid]['unit'] ?? null;
                if ($unitVal) {
                    $beratTry = is_numeric($unitVal) ? (float)$unitVal : null;
                    if ($beratTry === null && preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', (string)$unitVal, $m)) {
                        $beratTry = (float)str_replace(',', '.', $m[1]);
                    }
                    if ($beratTry !== null) $berat = $beratTry;
                }
                if ($berat === null) {
                    $skuStr = (string)$sku;
                    if (preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', $skuStr, $m)) {
                        $berat = (float)str_replace(',', '.', $m[1]);
                    }
                }
                $items[] = [
                    'sku' => $sku,
                    'name' => $name,
                    'qty' => $qty,
                    'berat_kg' => $berat,
                    'total_berat' => $berat !== null ? $berat * $qty : null,
                ];
            }
        }

        return view('pages/nota/detail', [ 'surat_jalan' => $sj, 'items' => $items ]);
    }

    public function print($id)
    {
        $sj = $this->suratJalanModel->find($id);
        if (!$sj) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat Jalan tidak ditemukan');
        }

        $pengirimanModel = new \App\Models\PengirimanModel();
        $pengiriman = $pengirimanModel->find($sj['id_pengiriman']);

        $customerModel = new \App\Models\CustomerModel();
        $customer = $customerModel->find($sj['id_customer'] ?? null);

    $pengirim = 'PT. Es hokita & Es Tubindo';
        $penerima = $customer ? ($customer['nama'] ?? '-') : '-';

        // Ambil barang langsung dari muatan surat jalan
        $barang = [];
        $muatan = [];
        if (!empty($sj['muatan'])) {
            $decoded = json_decode($sj['muatan'], true);
            if (is_array($decoded)) {
                $muatan = $decoded;
            }
        }

        if ($muatan) {
            $productModel = new \App\Models\ProductModel();
            $prodCache = [];
            foreach ($muatan as $i => $row) {
                if (isset($row['id_product'])) {
                    $pid = (int)$row['id_product'];
                    if (!isset($prodCache[$pid])) {
                        $prodCache[$pid] = $productModel->find($pid) ?: [];
                    }
                    $prod = $prodCache[$pid];
                    $sku = $prod['sku'] ?? ('#'.$pid);
                    $name = $prod['name'] ?? ('Produk #'.$pid);
                    
                    // Hitung berat satuan dari unit atau SKU
                    $berat = null;
                    $unitVal = $prod['unit'] ?? null;
                    if ($unitVal) {
                        $beratTry = is_numeric($unitVal) ? (float)$unitVal : null;
                        if ($beratTry === null && preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', (string)$unitVal, $m)) {
                            $beratTry = (float)str_replace(',', '.', $m[1]);
                        }
                        if ($beratTry !== null) $berat = $beratTry;
                    }
                    if ($berat === null && preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', $sku, $m)) {
                        $berat = (float)str_replace(',', '.', $m[1]);
                    }
                    
                    $barang[] = [
                        'kode'        => $sku,
                        'nama_barang' => $name,
                        'kuantitas'   => (float)($row['qty'] ?? 0),
                        'berat_kg'    => $berat,
                    ];
                } else {
                    // fallback untuk struktur lama
                    foreach ($muatan as $k => $v) {
                        $barang[] = [
                            'kode'        => strtoupper($k),
                            'nama_barang' => 'Es ' . ucfirst($k),
                            'kuantitas'   => is_numeric($v) ? (float)$v : 0,
                            'berat_kg'    => null,
                        ];
                    }
                    break;
                }
            }
        }

        $ruteModel = new \App\Models\RuteModel();
        $rute_info = $ruteModel->where('kode_rute', $sj['kode_rute'])->first();

        return view('pages/nota/print', [
            'pengirim' => $pengirim,
            'penerima' => $penerima,
            'barang'   => $barang,
            'rute'     => $rute_info ? $rute_info['nama_wilayah'] : ($sj['kode_rute'] ?? '-'),
        ]);
    }

    // Route alias to match Routes.php (printSuratJalan)
    public function printSuratJalan($id)
    {
        return $this->print($id);
    }

    /**
     * Cetak banyak Surat Jalan dalam satu file (1 halaman = 1 customer) untuk 1 BON/pengiriman.
     * URL: /surat-jalan/print-batch/{idPengiriman}?format=pdf (opsional)
     */
    public function printBatchByPengiriman($idPengiriman)
    {
        $db = \Config\Database::connect();
        $pengiriman = $db->table('pengiriman p')
            ->select('p.*, c.nama as customer_name_main, r.nama_wilayah as nama_wilayah')
            ->join('customer c', 'p.id_customer = c.id_customer', 'left')
            ->join('rute r', 'p.kode_rute = r.kode_rute', 'left')
            ->where('p.id_pengiriman', (int)$idPengiriman)
            ->get()->getRowArray();
        if (!$pengiriman) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengiriman tidak ditemukan');
        }

        // Ambil semua invoice yang terkait BON ini dan group per customer
        $invoices = $db->table('invoice i')
            ->select('i.id_invoice, i.id_transaction, t.items, t.transaction_no, c.id_customer, c.nama as customer_name, c.alamat as customer_address')
            ->join('transaction t', 'i.id_transaction = t.id_transaction', 'left')
            ->join('customer c', 't.id_customer = c.id_customer', 'left')
            ->where('i.id_pengiriman', (int)$idPengiriman)
            ->orderBy('c.nama', 'ASC')
            ->get()->getResultArray();

        if (!$invoices) {
            // Tidak ada invoice, fallback: satu halaman kosong untuk customer pengiriman
            $pages = [[
                'customer' => [
                    'id_customer' => $pengiriman['id_customer'] ?? null,
                    'name' => $pengiriman['customer_name_main'] ?? '-',
                    'address' => $pengiriman['alamat_customer'] ?? ($pengiriman['alamat'] ?? '-')
                ],
                'items' => []
            ]];
        } else {
            // Kelompokkan items per customer
            $productModel = new \App\Models\ProductModel();
            $prodCache = [];
            $pagesByCustomer = [];
            foreach ($invoices as $inv) {
                $cid = (int)($inv['id_customer'] ?? 0);
                if ($cid <= 0) continue;
                if (!isset($pagesByCustomer[$cid])) {
                    $pagesByCustomer[$cid] = [
                        'customer' => [
                            'id_customer' => $cid,
                            'name' => $inv['customer_name'] ?? '-',
                            'address' => $inv['customer_address'] ?? '-',
                        ],
                        'items' => []
                    ];
                }
                $items = json_decode($inv['items'] ?? '[]', true) ?: [];
                foreach ($items as $row) {
                    $pid = (int)($row['id_product'] ?? 0);
                    $qty = (float)($row['qty'] ?? 0);
                    if ($pid <= 0 || $qty <= 0) continue;
                    if (!isset($prodCache[$pid])) {
                        $p = $productModel->find($pid);
                        $prodCache[$pid] = $p ?: [];
                    }
                    $sku = $prodCache[$pid]['sku'] ?? ('#'.$pid);
                    $name = $prodCache[$pid]['name'] ?? ('Produk #'.$pid);
                    // Berat satuan (kg) mengikuti field produk (unit). Jika kosong, coba parse dari SKU (e.g. 10KG)
                    $berat = null;
                    $unitVal = $prodCache[$pid]['unit'] ?? null;
                    if ($unitVal !== null && $unitVal !== '') {
                        $beratTry = is_numeric($unitVal) ? (float)$unitVal : null;
                        if ($beratTry === null) {
                            if (preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', (string)$unitVal, $m)) {
                                $beratTry = (float)str_replace(',', '.', $m[1]);
                            }
                        }
                        if ($beratTry !== null) { $berat = $beratTry; }
                    }
                    if ($berat === null) {
                        $skuStr = (string)($prodCache[$pid]['sku'] ?? '');
                        if (preg_match('/(\d+(?:[\.,]\d+)?)\s*kg/i', $skuStr, $m)) {
                            $berat = (float)str_replace(',', '.', $m[1]);
                        }
                    }
                    $key = (string)$pid;
                    if (!isset($pagesByCustomer[$cid]['items'][$key])) {
                        $pagesByCustomer[$cid]['items'][$key] = [
                            'id_product' => $pid,
                            'kode' => $sku,
                            'nama' => $name,
                            'qty' => 0,
                            'berat_kg' => $berat,
                        ];
                    }
                    $pagesByCustomer[$cid]['items'][$key]['qty'] += $qty;
                }
            }
            // Flatten items to indexed arrays
            $pages = [];
            foreach ($pagesByCustomer as $cid => $page) {
                $page['items'] = array_values($page['items']);
                $pages[] = $page;
            }
        }

        $html = view('pages/nota/print_batch', [
            'pengiriman' => $pengiriman,
            'pages' => $pages,
        ]);

        $format = strtolower((string)($this->request->getGet('format') ?? $this->request->getGet('pdf') ?? ''));
        if ($format === '1' || $format === 'yes' || $format === 'true' || $format === 'pdf') {
            // Render PDF via Dompdf jika tersedia
            try {
                $dompdf = new \Dompdf\Dompdf([ 'isRemoteEnabled' => true ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $filename = 'SuratJalan-BON-'.$pengiriman['no_bon'].'-'.date('Ymd').'.pdf';
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
                    ->setBody($dompdf->output());
            } catch (\Throwable $th) {
                // Fallback ke HTML jika gagal
                return $html;
            }
        }
        return $html;
    }

    public function edit($id)
    {
        if ($this->request->getMethod() == 'POST') {
            $input = [
                'tanggal'       => $this->request->getPost('tanggal'),
                'kode_rute'     => $this->request->getPost('kode_rute'),
                'nama_penerima' => $this->request->getPost('nama_penerima') ?: null,
                'ttd_penerima'  => $this->request->getPost('ttd_penerima') ?: null,
                'ttd_produksi'  => $this->request->getPost('ttd_produksi') ?: null,
            ];

            if ($this->suratJalanModel->update($id, $input)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil diubah',
                    'url'     => '/surat-jalan',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $this->suratJalanModel->errors(),
            ]);
        }

        $data['surat_jalan'] = $this->suratJalanModel
            ->join('rute', 'nota.kode_rute = rute.kode_rute', 'left')
            ->find($id);
        $data['rutes'] = $this->ruteModel->findAll();

        return view('pages/nota/edit', $data);
    }

    public function delete($id)
    {
        $this->suratJalanModel->delete($id);
        return redirect()->to('/surat-jalan');
    }
}

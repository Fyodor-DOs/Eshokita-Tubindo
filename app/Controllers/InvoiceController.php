<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\PengirimanModel;

class InvoiceController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected PengirimanModel $pengirimanModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->pengirimanModel = new PengirimanModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        // Cek kolom mana yang ada di invoice table
        $fields = $db->getFieldNames('invoice');
        $hasPengiriman = in_array('id_pengiriman', $fields);
        $hasTransaction = in_array('id_transaction', $fields);
        $hasFotoSJ = in_array('foto_surat_jalan', $fields);
        $hasFotoTerima = in_array('foto_penerimaan', $fields);
        
        // Build query berdasarkan kolom yang tersedia
    if ($hasPengiriman && $hasTransaction) {
            // Dual system
            $invoices = $db->query("
                SELECT 
                    invoice.*,
                    COALESCE(pengiriman.no_bon, transaction.transaction_no) as ref_no,
                    COALESCE(pengiriman.tanggal, transaction.transaction_date) as ref_date,
                    customer.nama as customer_name
                FROM invoice
                LEFT JOIN pengiriman ON invoice.id_pengiriman = pengiriman.id_pengiriman
                LEFT JOIN transaction ON invoice.id_transaction = transaction.id_transaction
                LEFT JOIN customer ON (pengiriman.id_customer = customer.id_customer OR transaction.id_customer = customer.id_customer)
                ORDER BY invoice.created_at DESC
            ")->getResultArray();
        } elseif ($hasTransaction) {
            // Transaction only
            $invoices = $db->query("
                SELECT 
                    invoice.*,
                    transaction.transaction_no as ref_no,
                    transaction.transaction_date as ref_date,
                    customer.nama as customer_name
                FROM invoice
                JOIN transaction ON invoice.id_transaction = transaction.id_transaction
                JOIN customer ON transaction.id_customer = customer.id_customer
                ORDER BY invoice.created_at DESC
            ")->getResultArray();
        } else {
            // Pengiriman only (shouldn't happen after migration)
            $invoices = $db->query("
                SELECT 
                    invoice.*,
                    pengiriman.no_bon as ref_no,
                    pengiriman.tanggal as ref_date,
                    customer.nama as customer_name
                FROM invoice
                JOIN pengiriman ON invoice.id_pengiriman = pengiriman.id_pengiriman
                JOIN customer ON pengiriman.id_customer = customer.id_customer
                ORDER BY invoice.created_at DESC
            ")->getResultArray();
        }
        
        // Calculate total_paid for each invoice & inject status_pengiriman
        foreach ($invoices as &$invoice) {
            $payments = $db->table('payment')
                ->where('id_invoice', $invoice['id_invoice'])
                ->selectSum('amount', 'total_paid')
                ->get()
                ->getRowArray();
            $invoice['total_paid'] = $payments['total_paid'] ?? 0;

            // Prioritas: status berdasarkan foto per-invoice
            if ($hasFotoTerima && !empty($invoice['foto_penerimaan'])) {
                $invoice['status_pengiriman'] = 'diterima';
            } elseif ($hasFotoSJ && !empty($invoice['foto_surat_jalan'])) {
                $invoice['status_pengiriman'] = 'mengirim';
            } else {
                // Fallback: status dari pengiriman jika ada relasi
                if (!empty($invoice['id_pengiriman'])) {
                    $pengiriman = $db->table('pengiriman')->select('status')->where('id_pengiriman', $invoice['id_pengiriman'])->get()->getRowArray();
                    if (isset($pengiriman['status']) && $pengiriman['status']) {
                        $invoice['status_pengiriman'] = $pengiriman['status'];
                    } else {
                        $invoice['status_pengiriman'] = 'siap';
                    }
                } elseif (!empty($invoice['id_transaction'])) {
                    // Cek apakah kolom id_transaction ada di tabel pengiriman
                    $pengirimanFields = $db->getFieldNames('pengiriman');
                    if (in_array('id_transaction', $pengirimanFields)) {
                        $pengiriman = $db->table('pengiriman')->select('status')->where('id_transaction', $invoice['id_transaction'])->get()->getRowArray();
                        if (isset($pengiriman['status']) && $pengiriman['status']) {
                            $invoice['status_pengiriman'] = $pengiriman['status'];
                        } else {
                            $invoice['status_pengiriman'] = null;
                        }
                    } else {
                        $invoice['status_pengiriman'] = null;
                    }
                }
            }

            // Try fetch customer id and route for actions (may be null depending on join path)
            if (!isset($invoice['id_customer']) || !isset($invoice['kode_rute'])) {
                $cust = null;
                if (!empty($invoice['id_transaction'])) {
                    $cust = $db->query("SELECT c.id_customer, c.kode_rute FROM transaction t JOIN customer c ON t.id_customer=c.id_customer WHERE t.id_transaction=?", [$invoice['id_transaction']])->getRowArray();
                } elseif (!empty($invoice['id_pengiriman'])) {
                    $cust = $db->query("SELECT c.id_customer, c.kode_rute FROM pengiriman p JOIN customer c ON p.id_customer=c.id_customer WHERE p.id_pengiriman=?", [$invoice['id_pengiriman']])->getRowArray();
                }
                if ($cust) {
                    $invoice['id_customer'] = $cust['id_customer'];
                    $invoice['kode_rute'] = $cust['kode_rute'];
                }
            }

            // Default jika belum terisi: anggap 'siap'
            if (!isset($invoice['status_pengiriman']) || $invoice['status_pengiriman'] === null || $invoice['status_pengiriman'] === '') {
                $invoice['status_pengiriman'] = 'siap';
            }

            // After we have id_customer, if status_pengiriman still missing, infer from latest pengiriman of this customer
            if (!isset($invoice['status_pengiriman']) || $invoice['status_pengiriman'] === null) {
                if (!empty($invoice['id_customer'])) {
                    $row = $db->table('pengiriman')
                        ->select('status')
                        ->where('id_customer', (int)$invoice['id_customer'])
                        ->orderBy('tanggal', 'DESC')
                        ->orderBy('id_pengiriman', 'DESC')
                        ->get(1)
                        ->getRowArray();
                    if ($row && !empty($row['status'])) {
                        $invoice['status_pengiriman'] = $row['status'];
                    }
                }
            }
        }
        
        return view('pages/invoice/index', ['invoices' => $invoices]);
    }

    public function createFromPengiriman($idPengiriman)
    {
        // Ensure schema supports pengiriman reference
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('invoice');
        if (!in_array('id_pengiriman', $fields)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Skema invoice sekarang terhubung ke Transaksi, bukan Pengiriman. Silakan buat invoice dari Transaksi.']);
        }

        $pengiriman = $this->pengirimanModel->find($idPengiriman);
        if (!$pengiriman) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengiriman tidak ditemukan']);
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/invoice/confirm_from_pengiriman', ['pengiriman' => $pengiriman]);
        }

        $items = json_decode($pengiriman['pemesanan'] ?? '[]', true) ?: [];
        $amount = 0.0;
        foreach ($items as $item) {
            $amount += (float) ($item['total'] ?? 0);
        }

        $data = [
            'id_pengiriman' => $idPengiriman,
            'invoice_no' => $this->invoiceModel->generateInvoiceNo(),
            'issue_date' => $this->request->getPost('issue_date') ?: date('Y-m-d'),
            'due_date' => $this->request->getPost('due_date') ?: date('Y-m-d', strtotime('+7 days')),
            'amount' => $amount,
            'status' => 'unpaid',
        ];
        
        if ($this->invoiceModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Invoice berhasil dibuat', 'url' => '/invoice']);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => $this->invoiceModel->errors()]);
    }

    public function createFromTransaction($idTransaction)
    {
        $db = \Config\Database::connect();
        $transaction = $db->table('transaction')->where('id_transaction', $idTransaction)->get()->getRowArray();
        
        if (!$transaction) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/invoice/confirm_from_transaction', ['transaction' => $transaction]);
        }
        
        // Check if invoice already exists
        $existingInvoice = $db->table('invoice')->where('id_transaction', $idTransaction)->get()->getRowArray();
        if ($existingInvoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice sudah ada untuk transaksi ini']);
        }

        // Tidak mengurangi stok di tahap Invoice. Stok sudah dikurangi saat ORDER agar Product langsung menampilkan sisa stok.
        // Keputusan ini untuk mencegah double-deduct di kombinasi skema lama/baru dan race kondisi.

        $data = [
            'id_transaction' => $idTransaction,
            'invoice_no' => $this->invoiceModel->generateInvoiceNo(),
            'issue_date' => $this->request->getPost('issue_date') ?: date('Y-m-d'),
            'due_date' => $this->request->getPost('due_date') ?: date('Y-m-d', strtotime('+7 days')),
            'amount' => $transaction['total_amount'],
            'status' => 'unpaid',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($db->table('invoice')->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Invoice berhasil di-generate', 'url' => '/invoice']);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat invoice']);
    }
}

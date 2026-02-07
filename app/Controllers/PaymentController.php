<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\InvoiceModel;

/**
 * @property PaymentModel $paymentModel
 * @property InvoiceModel $invoiceModel
 */
class PaymentController extends BaseController
{
    protected PaymentModel $paymentModel;
    protected InvoiceModel $invoiceModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->invoiceModel = new InvoiceModel();
    }

    /**
     * Tampilkan daftar pembayaran untuk satu invoice
     * @param int|string $id Invoice ID
     */
    /**
     * Tampilkan daftar pembayaran untuk satu invoice
     */
    public function listByInvoice($id): \CodeIgniter\HTTP\ResponseInterface|string
    {
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice)
            return $this->response->setStatusCode(404)->setBody('Not found');
        $payments = $this->paymentModel->where('id_invoice', $id)->orderBy('paid_at', 'DESC')->findAll();
        return view('pages/payment/index', ['invoice' => $invoice, 'payments' => $payments]);
    }

    /**
     * Detail invoice beserta pembayaran
     * @param int|string $id Invoice ID
     */
    /**
     * Detail invoice beserta pembayaran
     */
    public function detail($id): \CodeIgniter\HTTP\ResponseInterface|string
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();
        // Get invoice with customer and transaction details
        /** @var \\CodeIgniter\\Database\\BaseBuilder $builder */
        $builder = $db->table('invoice as i');
        $invoice = $builder
            ->select(
                'i.*, c.nama as customer_name, c.email, c.telepon, c.alamat, '
                . 't.transaction_no, t.items as transaction_items, t.transaction_date, '
                . 'r.nama_wilayah as rute_name, '
                . 'p.no_bon, p.status as pengiriman_status'
            )
            ->join('transaction as t', 't.id_transaction = i.id_transaction', 'left')
            ->join('customer as c', 'c.id_customer = t.id_customer', 'left')
            ->join('rute as r', 'r.kode_rute = c.kode_rute', 'left')
            ->join('pengiriman as p', 'p.id_pengiriman = i.id_pengiriman', 'left')
            ->where('i.id_invoice', $id)
            ->get()
            ->getRowArray();

        if (!$invoice) {
            return $this->response->setStatusCode(404)->setBody('Invoice tidak ditemukan');
        }

        // Parse transaction items
        $items = [];
        if (!empty($invoice['transaction_items'])) {
            $items = json_decode($invoice['transaction_items'], true) ?: [];

            // Fix unknown products - lookup from database if name not exists
            foreach ($items as &$item) {
                if (empty($item['name']) || $item['name'] === 'Unknown') {
                    $idProduct = (int) ($item['id_product'] ?? 0);
                    if ($idProduct > 0) {
                        $product = $db->table('product p')
                            ->select('p.name, p.unit, pc.name as category_name')
                            ->join('product_category pc', 'pc.id_category = p.id_category', 'left')
                            ->where('p.id_product', $idProduct)
                            ->get()
                            ->getRowArray();
                        if ($product) {
                            $item['name'] = $product['category_name'] . ' ' . $product['unit'] . 'kg';
                        }
                    }
                }
            }
            unset($item);
        }

        // Get all payments for this invoice
        $payments = $this->paymentModel
            ->where('id_invoice', $id)
            ->orderBy('paid_at', 'ASC')
            ->findAll();

        // Calculate payment summary
        $totalPaid = array_sum(array_column($payments, 'amount'));
        $remaining = $invoice['amount'] - $totalPaid;
        $isPaid = $remaining <= 0;

        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining,
            'isPaid' => $isPaid
        ];

        return view('pages/payment/detail', $data);
    }

    /**
     * Form & proses tambah pembayaran (Cash dengan upload foto)
     * @param int|string $id Invoice ID
     */
    public function create($id): \CodeIgniter\HTTP\ResponseInterface|string
    {
        if ($this->request->getMethod() !== 'POST') {
            $invoice = $this->invoiceModel->find($id);
            if (!$invoice) {
                return redirect()->to('/invoice')->with('error', 'Invoice tidak ditemukan');
            }
            return view('pages/payment/create', ['invoice' => $invoice]);
        }

        // Handle file upload for Cash payment
        $invoicePhoto = null;
        $file = $this->request->getFile('invoice_photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'invoice_' . time() . '_' . $file->getRandomName();
            $file->move(FCPATH . 'uploads/invoices', $newName);
            $invoicePhoto = $newName;
        }

        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice tidak ditemukan']);
        }

        // Calculate remaining amount properly
        $paidSum = $this->paymentModel->where('id_invoice', $id)->selectSum('amount')->first();
        $totalPaid = (float) ($paidSum['amount'] ?? 0);
        $invoiceAmount = (float) $invoice['amount'];
        $remaining = $invoiceAmount - $totalPaid;

        // If already fully paid
        if ($remaining <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invoice sudah lunas',
                'url' => base_url('/invoice')
            ]);
        }

        // Use remaining amount to prevent overpayment
        $amountToPay = $remaining;

        // Normalize and guard payment method
        $method = strtolower($this->request->getPost('method') ?: 'cash');
        $allowedMethods = ['cash', 'kredit', 'transfer', 'qris', 'va', 'ewallet', 'other'];
        if (!in_array($method, $allowedMethods, true)) {
            $method = 'other';
        }

        $data = [
            'id_invoice' => $id,
            'paid_at' => $this->request->getPost('paid_at') ?: date('Y-m-d H:i:s'),
            'method' => $method,
            'amount' => $amountToPay,
            'note' => $this->request->getPost('note'),
            'invoice_photo' => $invoicePhoto,
        ];
        if ($this->paymentModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan',
                'url' => base_url('/invoice')
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => $this->paymentModel->errors(),
            'url' => null
        ]);
    }

    /**
     * Gateway halaman simulasi pembayaran (QRIS, VA, E-Wallet)
     * @param int|string $id Invoice ID
     */
    public function gateway($id): \CodeIgniter\HTTP\ResponseInterface|string
    {
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) {
            return redirect()->to('/invoice')->with('error', 'Invoice tidak ditemukan');
        }

        // Get payment method from query string
        $method = $this->request->getGet('method') ?: 'qris';
        $allowedMethods = ['qris', 'va', 'ewallet'];
        if (!in_array($method, $allowedMethods, true)) {
            $method = 'qris';
        }

        // Calculate outstanding amount
        $paidSum = $this->paymentModel->where('id_invoice', $id)->selectSum('amount')->first();
        $totalPaid = (float) ($paidSum['amount'] ?? 0);
        $amount = max(0, (float) $invoice['amount'] - $totalPaid);

        // If already fully paid, redirect
        if ($amount <= 0) {
            return redirect()->to('/invoice')->with('info', 'Invoice sudah lunas');
        }

        return view('pages/payment/gateway', [
            'invoice' => $invoice,
            'method' => $method,
            'amount' => $amount
        ]);
    }

    /**
     * Process payment from gateway simulation
     * @param int|string $id Invoice ID
     */
    public function process($id): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice tidak ditemukan']);
        }

        // Calculate remaining amount properly
        $paidSum = $this->paymentModel->where('id_invoice', $id)->selectSum('amount')->first();
        $totalPaid = (float) ($paidSum['amount'] ?? 0);
        $invoiceAmount = (float) $invoice['amount'];
        $remaining = $invoiceAmount - $totalPaid;

        // If already fully paid
        if ($remaining <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invoice sudah lunas',
            ]);
        }

        // Use remaining amount to prevent overpayment
        $amountToPay = $remaining;

        $method = strtolower($this->request->getPost('method') ?: 'other');
        $allowedMethods = ['qris', 'va', 'ewallet', 'cash', 'kredit', 'transfer', 'other'];
        if (!in_array($method, $allowedMethods, true)) {
            $method = 'other';
        }

        // Generate transaction reference for digital payments
        $transactionRef = strtoupper($method) . '-' . date('YmdHis') . '-' . $id;

        $data = [
            'id_invoice' => $id,
            'paid_at' => $this->request->getPost('paid_at') ?: date('Y-m-d H:i:s'),
            'method' => $method,
            'amount' => $amountToPay,
            'note' => $this->request->getPost('note') . ' | Ref: ' . $transactionRef,
            'invoice_photo' => null, // Digital payments don't need photo
        ];

        if ($this->paymentModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pembayaran berhasil diterima',
                'transaction_ref' => $transactionRef,
                'url' => base_url('/invoice')
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan pembayaran',
        ]);
    }
}

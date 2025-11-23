<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\InvoiceModel;

class TransactionController extends BaseController
{
    protected $customerModel;
    protected $productModel;
    protected $invoiceModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->invoiceModel = new InvoiceModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transaction');
        $builder->select('transaction.*, customer.nama as customer_name, customer.alamat as customer_address');
        $builder->join('customer', 'transaction.id_customer = customer.id_customer');
        $builder->orderBy('transaction.created_at', 'DESC');
        $transactions = $builder->get()->getResultArray();

        return view('pages/transaction/index', ['transactions' => $transactions]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            // Get customer & items
            $idCustomer = $this->request->getPost('id_customer');
            $items = $this->request->getPost('items'); // could be JSON string
            if (is_string($items)) {
                $decoded = json_decode($items, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $items = $decoded;
                }
            }
            
            if (empty($items)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Minimal harus ada 1 produk']);
            }

            $total = 0;
            $itemsData = [];
            
            foreach ($items as $item) {
                $productId = isset($item['id_product']) ? $item['id_product'] : ($item['id'] ?? null);
                if (!$productId) continue;
                $product = $this->productModel->find($productId);
                if (!$product) continue;
                
                $qty = (int) $item['qty'];
                $price = (float) $item['price'];
                $subtotal = $qty * $price;
                $total += $subtotal;
                
                $itemsData[] = [
                    'id_product' => $product['id_product'],
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
                
                // Update stock
                $newQty = $product['qty'] - $qty;
                if ($newQty < 0) {
                    return $this->response->setJSON(['success' => false, 'message' => "Stok {$product['name']} tidak cukup"]);
                }
                $this->productModel->update($product['id_product'], ['qty' => $newQty]);
            }

            $transactionNo = $this->generateTransactionNo();
            
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Insert transaction
            $db->table('transaction')->insert([
                'transaction_no' => $transactionNo,
                'id_customer' => $idCustomer,
                'transaction_date' => date('Y-m-d H:i:s'),
                'items' => json_encode($itemsData),
                'total_amount' => $total,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $idTransaction = $db->insertID();
            
            // Auto create invoice
            $invoiceNo = $this->invoiceModel->generateInvoiceNo();
            $db->table('invoice')->insert([
                'id_transaction' => $idTransaction,
                'invoice_no' => $invoiceNo,
                'issue_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'amount' => $total,
                'status' => 'unpaid',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat transaksi']);
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'url' => '/transaction']);
        }

        $customers = $this->customerModel->orderBy('nama', 'ASC')->findAll();
        $products = $this->productModel->where('qty >', 0)->orderBy('name', 'ASC')->findAll();
        
        return view('pages/transaction/create', [
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transaction');
        $builder->select('transaction.*, customer.nama as customer_name, customer.alamat as customer_address, customer.telp as customer_phone');
        $builder->join('customer', 'transaction.id_customer = customer.id_customer');
        $builder->where('transaction.id_transaction', $id);
        $transaction = $builder->get()->getRowArray();
        
        if (!$transaction) {
            return redirect()->to('/transaction');
        }
        
        // Get invoice if exists
        $invoice = $db->table('invoice')
            ->where('id_transaction', $id)
            ->get()
            ->getRowArray();
        
        return view('pages/transaction/detail', [
            'transaction' => $transaction,
            'invoice' => $invoice
        ]);
    }
    
    public function cancel($id)
    {
        $db = \Config\Database::connect();
        $transaction = $db->table('transaction')->where('id_transaction', $id)->get()->getRowArray();
        
        if (!$transaction || $transaction['status'] !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak dapat dibatalkan']);
        }
        
        $db->transStart();
        
        // Restore stock
        $items = json_decode($transaction['items'], true);
        foreach ($items as $item) {
            $product = $this->productModel->find($item['id_product']);
            if ($product) {
                $newQty = $product['qty'] + $item['qty'];
                $this->productModel->update($item['id_product'], ['qty' => $newQty]);
            }
        }
        
        // Update transaction status
        $db->table('transaction')->where('id_transaction', $id)->update([
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan transaksi']);
        }
        
        return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil dibatalkan, stok produk telah dikembalikan']);
    }

    private function generateTransactionNo()
    {
        $date = date('Ymd');
        $db = \Config\Database::connect();
        $builder = $db->table('transaction');
        $builder->like('transaction_no', 'TRX-' . $date, 'after');
        $count = $builder->countAllResults();
        return 'TRX-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}

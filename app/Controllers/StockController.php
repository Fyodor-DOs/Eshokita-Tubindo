<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockModel;
use App\Models\StockTransactionModel;
use App\Models\ProductModel;

class StockController extends BaseController
{
    protected $stockModel;
    protected $txModel;
    protected $productModel;

    public function __construct()
    {
        $this->stockModel = new StockModel();
        $this->txModel = new StockTransactionModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Tampilkan list stock semua produk
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $products = $db->table('product')
            ->select('product.*, product_category.nama as category_name')
            ->join('product_category', 'product.id_product_category = product_category.id_product_category', 'left')
            ->orderBy('product.qty', 'ASC')
            ->get()
            ->getResultArray();
        
        return view('pages/stock/index', ['products' => $products]);
    }

    /**
     * Form update stock produk
     */
    public function update($id)
    {
        log_message('info', 'StockController::update called. Method='.$this->request->getMethod().' ID='.$id);
        // Handle POST request (update stock) - harus dicek duluan sebelum view
        if ($this->request->getMethod() === 'post') {
            return $this->processUpdateStock($id);
        }
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return redirect()->to('/stock')->with('error', 'Produk tidak ditemukan');
        }

        $stock = $this->stockModel->getByProduct($id);

        // Tampilkan form
        return view('pages/stock/update', [
            'product' => $product,
            'stock' => $stock
        ]);
    }

    /**
     * Process update stock
     */
    private function processUpdateStock($id)
    {
        // Selalu balas dengan JSON untuk POST update stok
        $qty = (int) $this->request->getPost('qty');
        $action = $this->request->getPost('action');
        log_message('info', 'processUpdateStock start: id='.$id.', action='.$action.', qty='.$qty);

        // Validasi
        if ($qty < 1) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Jumlah harus lebih dari 0'
                ]);
        }

        if (!in_array($action, ['in', 'out'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Action tidak valid. Gunakan "in" atau "out"'
                ]);
        }

        // Record transaction
        try {
            log_message('info', 'Update stock - Product ID: ' . $id . ', Action: ' . $action . ', Qty: ' . $qty);
            
            $result = $this->txModel->recordTransaction(
                $id,
                $action,
                $qty,
                'manual',
                null,
                'Update manual dari form'
            );

            if ($result) {
                log_message('info', 'Stock updated successfully for product ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Stock berhasil diupdate',
                    'url' => base_url('/stock'),
                ]);
            }

            $errors = $this->txModel->errors();
            $txLast = method_exists($this->txModel, 'getLastError') ? $this->txModel->getLastError() : null;
            $errorMsg = $txLast ?: (!empty($errors) ? implode(', ', $errors) : 'Gagal update stock');
            log_message('error', 'Failed to update stock: ' . $errorMsg);
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                ]);
        } catch (\Exception $e) {
            log_message('error', 'Exception update stock: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
        }
    }
}

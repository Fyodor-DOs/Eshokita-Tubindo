<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'id_stock';
    protected $returnType = 'array';
    protected $allowedFields = ['id_product', 'qty', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Update atau create stock untuk product
     */
    public function updateStock(int $idProduct, int $qtyChange): bool
    {
        $db = \Config\Database::connect();

        // If stock table does NOT exist, fallback to updating product.qty directly
        try {
            $stockTableExists = method_exists($db, 'tableExists') ? $db->tableExists($this->table) : in_array($this->table, $db->listTables());
        } catch (\Throwable $th) {
            $stockTableExists = false;
        }

        if (!$stockTableExists) {
            try {
                $prod = $db->table('product')->select('qty')->where('id_product', $idProduct)->get()->getRowArray();
                $current = (int) ($prod['qty'] ?? 0);
                $newQty = max(0, $current + $qtyChange);
                log_message('debug', "Stock update (fallback): product={$idProduct}, current={$current}, change={$qtyChange}, newQty={$newQty}");
                $ok = $db->table('product')->where('id_product', $idProduct)->update([
                    'qty' => $newQty,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                log_message('debug', "Stock update (fallback) result: " . ($ok ? 'success' : 'failed'));
                return (bool) $ok;
            } catch (\Throwable $th) {
                log_message('error', 'updateStock fallback failed: ' . $th->getMessage());
                return false;
            }
        }

        // Normal path using stock table
        $stock = $this->where('id_product', $idProduct)->first();

        if ($stock) {
            // Update existing stock
            $newQty = max(0, $stock['qty'] + $qtyChange);
            $result = $this->update($stock['id_stock'], [
                'qty' => $newQty,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($result === false) {
                return false;
            }
            // Sinkronkan juga ke kolom qty di table product (agar halaman Product menampilkan stok terbaru)
            try {
                $db->table('product')->where('id_product', $idProduct)->update([
                    'qty' => $newQty,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable $th) { /* ignore */
            }
            return true;
        } else {
            // Create new stock entry based on current product qty + delta
            try {
                $db = \Config\Database::connect();
                $prod = $db->table('product')->select('qty')->where('id_product', $idProduct)->get()->getRowArray();
                $currentProdQty = (int) ($prod['qty'] ?? 0);
            } catch (\Throwable $th) {
                $currentProdQty = 0;
            }
            $computedQty = $currentProdQty + $qtyChange; // qtyChange can be negative
            $initialQty = max(0, $computedQty);
            $result = $this->insert([
                'id_product' => $idProduct,
                'qty' => $initialQty,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($result === false) {
                return false;
            }
            // Sinkronkan juga ke kolom qty di table product
            try {
                $db->table('product')->where('id_product', $idProduct)->update([
                    'qty' => $initialQty,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable $th) { /* ignore */
            }
            return true;
        }
    }

    /**
     * Get stock dengan info product
     */
    public function getStockWithProduct()
    {
        return $this->select('stock.*, product.name as product_name, product.sku')
            ->join('product', 'stock.id_product = product.id_product')
            ->orderBy('product.name', 'ASC')
            ->findAll();
    }

    /**
     * Get stock by product id
     */
    public function getByProduct(int $idProduct)
    {
        return $this->where('id_product', $idProduct)->first();
    }
}

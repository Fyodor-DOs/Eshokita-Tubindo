<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTransactionModel extends Model
{
    protected $table = 'stock_transaction';
    protected $primaryKey = 'id_stock_tx';
    protected $returnType = 'array';
    protected $allowedFields = ['id_product', 'type', 'qty', 'ref_type', 'ref_id', 'note', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Holds last error message for troubleshooting
     */
    protected ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Record transaksi stock dan update stock
     */
    public function recordTransaction(int $idProduct, string $type, int $qty, ?string $refType = null, ?int $refId = null, ?string $note = null): bool
    {
        // Validasi type
        if (!in_array($type, ['in', 'out'])) {
            log_message('error', 'Invalid stock transaction type: ' . $type);
            return false;
        }

        // Pastikan qty positif
        $qty = abs($qty);

    // Reset last error
    $this->lastError = null;

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert transaction record only if table exists
            $txTable = $this->table;
            $txTableExists = false;
            try {
                $txTableExists = method_exists($db, 'tableExists') ? $db->tableExists($txTable) : in_array($txTable, $db->listTables());
            } catch (\Throwable $th) { $txTableExists = false; }

            if ($txTableExists) {
                $insertResult = $this->insert([
                    'id_product' => $idProduct,
                    'type' => $type,
                    'qty' => $qty,
                    'ref_type' => $refType,
                    'ref_id' => $refId,
                    'note' => $note,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if ($insertResult === false) {
                    $this->lastError = 'Gagal insert transaksi: ' . json_encode($this->errors());
                    log_message('error', $this->lastError);
                    $db->transRollback();
                    return false;
                }
            }

            // Update stock
            $delta = ($type === 'in') ? $qty : -$qty;
            $stockModel = new StockModel();
            $updateResult = $stockModel->updateStock($idProduct, $delta);

            if (!$updateResult) {
                $stockErrors = method_exists($stockModel, 'errors') ? $stockModel->errors() : [];
                $this->lastError = 'Gagal update stok' . (!empty($stockErrors) ? ': ' . json_encode($stockErrors) : '');
                log_message('error', $this->lastError);
                $db->transRollback();
                return false;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                $this->lastError = 'Transaksi database gagal' . (!empty($dbError['message']) ? ': ' . $dbError['message'] : '');
                log_message('error', $this->lastError);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            $this->lastError = $e->getMessage();
            log_message('error', 'Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get history transaksi by product
     */
    public function getHistoryByProduct(int $idProduct)
    {
        return $this->where('id_product', $idProduct)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}

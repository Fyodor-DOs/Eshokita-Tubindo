<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class InvoiceModel extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'id_invoice';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_invoice', 'id_transaction', 'id_pengiriman', 'invoice_no', 'issue_date', 'due_date', 'amount', 'status', 'foto_surat_jalan', 'foto_penerimaan'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateAutoId'];

    protected ?string $lastGeneratedId = null;

    protected function generateAutoId(array $data): array
    {
        if (!isset($data['data'][$this->primaryKey]) || empty($data['data'][$this->primaryKey])) {
            $id = IdGenerator::generateForTable($this->table, $this->primaryKey);
            $data['data'][$this->primaryKey] = $id;
            $this->lastGeneratedId = $id;
        } else {
            $this->lastGeneratedId = $data['data'][$this->primaryKey];
        }
        return $data;
    }

    public function getGeneratedId(): ?string
    {
        return $this->lastGeneratedId;
    }

    public function generateInvoiceNo(): string
    {
        $prefix = 'INV-' . date('y') . date('m'); // INV-2602
        $db = \Config\Database::connect();

        $result = $db->table($this->table)
            ->select('invoice_no')
            ->like('invoice_no', $prefix, 'after')
            ->orderBy('invoice_no', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($result) {
            $lastSeq = (int) substr($result['invoice_no'], -3);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . '-' . str_pad((string) $newSeq, 3, '0', STR_PAD_LEFT);
    }
}

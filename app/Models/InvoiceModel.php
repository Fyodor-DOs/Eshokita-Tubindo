<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoice';
    protected $primaryKey       = 'id_invoice';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pengiriman', 'invoice_no', 'issue_date', 'due_date', 'amount', 'status', 'foto_surat_jalan', 'foto_penerimaan'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateInvoiceNo(): string
    {
        $num = 1;
        do {
            $no = 'INV-' . date('Ymd') . '-' . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
            $exists = $this->where('invoice_no', $no)->first();
            $num++;
        } while ($exists);
        return $no;
    }
}

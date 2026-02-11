<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class PaymentModel extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'id_payment';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_payment', 'id_invoice', 'paid_at', 'method', 'amount', 'note', 'invoice_photo'];

    protected $useTimestamps = false;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateAutoId'];
    protected $afterInsert = ['updateInvoiceStatus'];

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

    protected function updateInvoiceStatus(array $data)
    {
        if (!isset($data['data']['id_invoice']))
            return $data;

        $idInvoice = $data['data']['id_invoice'];
        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->find($idInvoice);
        if (!$invoice)
            return $data;

        $totalPaid = $this->where('id_invoice', $idInvoice)->selectSum('amount')->first()['amount'] ?? 0;
        $amount = (float) $invoice['amount'];
        $status = 'unpaid';
        if ($totalPaid <= 0) {
            $status = 'unpaid';
        } elseif ($totalPaid < $amount) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }
        $invoiceModel->update($idInvoice, ['status' => $status]);
        return $data;
    }
}

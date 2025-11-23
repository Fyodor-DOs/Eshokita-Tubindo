<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payment';
    protected $primaryKey       = 'id_payment';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_invoice', 'paid_at', 'method', 'amount', 'note', 'invoice_photo'];

    protected $useTimestamps = false;

    protected $afterInsert = ['updateInvoiceStatus'];

    protected function updateInvoiceStatus(array $data)
    {
        if (!isset($data['data']['id_invoice'])) return $data;

        $idInvoice = (int) $data['data']['id_invoice'];
        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->find($idInvoice);
        if (!$invoice) return $data;

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

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenerimaanModel;
use App\Models\PengirimanModel;
use App\Models\CustomerModel;

/**
 * @property PenerimaanModel $penerimaanModel
 * @property PengirimanModel $pengirimanModel
 * @property CustomerModel   $customerModel
 */
class PenerimaanController extends BaseController
{
    protected PenerimaanModel $penerimaanModel;
    protected PengirimanModel $pengirimanModel;
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->penerimaanModel = new PenerimaanModel();
        $this->pengirimanModel = new PengirimanModel();
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();
        $list = $db->query("
            SELECT pnr.*, pg.no_bon, pg.kode_rute, c.nama as customer_name
            FROM penerimaan pnr
            JOIN pengiriman pg ON pnr.id_pengiriman = pg.id_pengiriman
            LEFT JOIN customer c ON pnr.id_customer = c.id_customer
            ORDER BY pnr.created_at DESC
        ")->getResultArray();
        return view('pages/penerimaan/index', ['penerimaan' => $list]);
    }

    public function create($idPengiriman)
    {
        $pengiriman = $this->pengirimanModel->find($idPengiriman);
        if (!$pengiriman)
            return redirect()->to('/pengiriman');

        if ($this->request->getMethod() !== 'POST') {
            // derive customer options from route
            $customers = $this->customerModel->where('kode_rute', $pengiriman['kode_rute'])->orderBy('nama', 'ASC')->findAll();
            return view('pages/penerimaan/create', [
                'pengiriman' => $pengiriman,
                'customers' => $customers,
            ]);
        }

        // handle upload
        $photoPath = null;
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'receipt_' . time() . '_' . $file->getRandomName();
            $file->move(FCPATH . 'uploads/receipts', $newName);
            $photoPath = $newName;
        }

        // items_received as JSON string or array
        $itemsReceived = $this->request->getPost('items_received');
        if (is_string($itemsReceived)) {
            // try decode to validate JSON
            $decoded = json_decode($itemsReceived, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $itemsReceived = json_encode($decoded);
            }
        } elseif (is_array($itemsReceived)) {
            $itemsReceived = json_encode($itemsReceived);
        } else {
            $itemsReceived = null;
        }

        $data = [
            'id_pengiriman' => $idPengiriman,
            'id_customer' => $this->request->getPost('id_customer') ?: null,
            'received_at' => $this->request->getPost('received_at') ?: date('Y-m-d H:i:s'),
            'receiver_name' => $this->request->getPost('receiver_name'),
            'status' => $this->request->getPost('status') ?: 'received',
            'items_received' => $itemsReceived,
            'photo_path' => $photoPath,
            'note' => $this->request->getPost('note'),
        ];

        if ($this->penerimaanModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Penerimaan dicatat', 'url' => '/penerimaan']);
        }
        return $this->response->setJSON(['success' => false, 'message' => $this->penerimaanModel->errors()]);
    }

    public function verify($id)
    {
        $userId = session()->get('user_id') ?: null;
        $ok = $this->penerimaanModel->update($id, [
            'verified' => 1,
            'verified_by' => $userId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}

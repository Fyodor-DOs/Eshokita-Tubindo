<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ShipmentTrackingModel;

class ShipmentTrackingController extends BaseController
{
    protected ShipmentTrackingModel $trackingModel;

    public function __construct()
    {
        $this->trackingModel = new ShipmentTrackingModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $tracking = $db->query("
            SELECT 
                shipment_tracking.*,
                pengiriman.no_bon,
                customer.nama as customer_name
            FROM shipment_tracking
            JOIN pengiriman ON shipment_tracking.id_pengiriman = pengiriman.id_pengiriman
            JOIN customer ON pengiriman.id_customer = customer.id_customer
            ORDER BY shipment_tracking.created_at DESC
        ")->getResultArray();

        return view('pages/tracking/index', ['tracking' => $tracking]);
    }

    public function list($idPengiriman)
    {
        $timeline = $this->trackingModel
            ->where('id_pengiriman', $idPengiriman)
            ->orderBy('created_at', 'ASC')
            ->findAll();
        return $this->response->setJSON(['success' => true, 'data' => $timeline]);
    }

    public function create($idPengiriman)
    {
        $data = [
            'id_pengiriman' => $idPengiriman,
            'status' => $this->request->getPost('status'),
            'location' => $this->request->getPost('location'),
            'note' => $this->request->getPost('note'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if ($this->trackingModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tracking ditambahkan']);
        }
        return $this->response->setJSON(['success' => false, 'message' => $this->trackingModel->errors()]);
    }
}

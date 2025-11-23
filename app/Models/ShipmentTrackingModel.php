<?php

namespace App\Models;

use CodeIgniter\Model;

class ShipmentTrackingModel extends Model
{
    protected $table            = 'shipment_tracking';
    protected $primaryKey       = 'id_tracking';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pengiriman', 'status', 'location', 'note', 'created_at'];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pengiriman' => 'required|integer',
        'status' => 'required|in_list[created,on-route,delivered,failed,returned]',
    ];
}

<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class ShipmentTrackingModel extends Model
{
    protected $table = 'shipment_tracking';
    protected $primaryKey = 'id_tracking';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_tracking', 'id_pengiriman', 'status', 'location', 'note', 'created_at'];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pengiriman' => 'required',
        'status' => 'required|in_list[created,on-route,delivered,failed,returned]',
    ];

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
}

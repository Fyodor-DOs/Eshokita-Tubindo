<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class PenerimaanModel extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'id_penerimaan';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_penerimaan',
        'id_pengiriman',
        'id_customer',
        'received_at',
        'receiver_name',
        'status',
        'items_received',
        'photo_path',
        'note',
        'verified',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id_pengiriman' => 'required',
        'status' => 'in_list[received,partial,failed]',
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

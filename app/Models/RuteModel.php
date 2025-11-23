<?php

namespace App\Models;

use CodeIgniter\Model;

class RuteModel extends Model
{
    protected $table            = 'rute';
    protected $primaryKey       = 'id_rute';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_rute', 'nama_wilayah'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'kode_rute'    => 'required|is_unique[rute.kode_rute,id_rute,{id_rute}]',
        'nama_wilayah' => 'required',
    ];
    protected $validationMessages   = [
        'kode_rute'    => [
            'required' => 'Kode Rute tidak boleh kosong',
            'is_unique' => 'Kode Rute sudah ada',
        ],
        'nama_wilayah' => [
            'required' => 'Nama Wilayah harus diisi',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $table            = 'nota';
    protected $primaryKey       = 'id_surat_jalan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanggal',
        'kode_rute',
        'muatan',
        'ttd_produksi',
        // new relations/receiver info
        'id_pengiriman',
        'id_customer',
        'nama_penerima',
        'ttd_penerima',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'tanggal' => 'required',
        'kode_rute' => 'required',
        'muatan' => 'required',
        // optional: id_pengiriman/id_customer may be null for legacy
    ];
    protected $validationMessages   = [
        'tanggal' => [
            'required' => 'Tanggal tidak boleh kosong',
        ],
        'kode_rute' => [
            'required' => 'Rute harus diisi.',
        ],
        'muatan' => [
            'required' => 'Muatan harus diisi.',
        ],
        // ttd_produksi optional in this flow
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
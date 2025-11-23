<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customer';
    protected $primaryKey       = 'id_customer';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_rute', 'nama', 'email', 'telepon', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'kodepos', 'alamat', 'produk', 'order_items'];

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
        'kode_rute' => 'required',
        'nama' => 'required',
    // Email tidak wajib diisi; jika diisi harus format valid. Uniqueness diabaikan sesuai permintaan.
    'email' => 'permit_empty|valid_email',
    // Telepon wajib diisi; uniqueness dihapus untuk mencegah false positive saat create
    'telepon' => 'required',
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'kecamatan' => 'required',
        'kelurahan' => 'required',
        'kodepos' => 'required',
        'alamat' => 'required',
        'produk' => 'required',
    ];
    protected $validationMessages   = [
        'kode_rute' => [
            'required' => 'Rute harus diisi',
        ],
        'nama' => [
            'required' => 'Nama harus diisi',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
        ],
        'telepon' => [
            'required' => 'Telepon harus diisi',
        ],
        'provinsi' => [
            'required' => 'Provinsi harus diisi',
        ],
        'kabupaten' => [
            'required' => 'Kabupaten harus diisi',
        ],
        'kecamatan' => [
            'required' => 'Kecamatan harus diisi',
        ],
        'kelurahan' => [
            'required' => 'Kelurahan harus diisi',
        ],
        'kodepos' => [
            'required' => 'Kodepos harus diisi',
        ],
        'alamat' => [
            'required' => 'Alamat harus diisi',
        ],
        'produk' => [
            'required' => 'Produk harus diisi',
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

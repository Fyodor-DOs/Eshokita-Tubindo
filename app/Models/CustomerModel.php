<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class CustomerModel extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'id_customer';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_customer', 'kode_rute', 'nama', 'email', 'telepon', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'kodepos', 'alamat', 'produk', 'order_items'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode_rute' => 'required',
        'nama' => 'required',
        'email' => 'permit_empty|valid_email',
        'telepon' => 'required',
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'kecamatan' => 'required',
        'kelurahan' => 'required',
        'kodepos' => 'required',
        'alamat' => 'required',
        'produk' => 'required',
    ];
    protected $validationMessages = [
        'kode_rute' => ['required' => 'Rute harus diisi'],
        'nama' => ['required' => 'Nama harus diisi'],
        'email' => ['valid_email' => 'Format email tidak valid'],
        'telepon' => ['required' => 'Telepon harus diisi'],
        'provinsi' => ['required' => 'Provinsi harus diisi'],
        'kabupaten' => ['required' => 'Kabupaten harus diisi'],
        'kecamatan' => ['required' => 'Kecamatan harus diisi'],
        'kelurahan' => ['required' => 'Kelurahan harus diisi'],
        'kodepos' => ['required' => 'Kodepos harus diisi'],
        'alamat' => ['required' => 'Alamat harus diisi'],
        'produk' => ['required' => 'Produk harus diisi'],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateAutoId'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

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

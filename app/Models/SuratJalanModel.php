<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class SuratJalanModel extends Model
{
    protected $table = 'nota';
    protected $primaryKey = 'id_surat_jalan';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_surat_jalan',
        'tanggal',
        'kode_rute',
        'muatan',
        'ttd_produksi',
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
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tanggal' => 'required',
        'kode_rute' => 'required',
        'muatan' => 'required',
    ];
    protected $validationMessages = [
        'tanggal' => ['required' => 'Tanggal tidak boleh kosong'],
        'kode_rute' => ['required' => 'Rute harus diisi.'],
        'muatan' => ['required' => 'Muatan harus diisi.'],
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
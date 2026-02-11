<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\IdGenerator;

class PengirimanModel extends Model
{
    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_pengiriman',
        'tanggal',
        'no_bon',
        'supir',
        'kenek',
        'plat_kendaraan',
        'kode_rute',
        'id_customer',
        'nama_penerima',
        'pembayaran',
        'pemesanan',
        'ttd_penerima',
        'status',
        'foto_surat_jalan',
        'foto_penerimaan'
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
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateAutoId', 'setValidationForCreate'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['setValidationForUpdate'];
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

    public function generateNoBon()
    {
        $prefix = 'BON-' . date('Ymd'); // BON-20260211
        $db = \Config\Database::connect();

        $result = $db->table($this->table)
            ->select('no_bon')
            ->like('no_bon', $prefix, 'after')
            ->orderBy('no_bon', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($result) {
            $lastSeq = (int) substr($result['no_bon'], -3);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . '-' . str_pad((string) $newSeq, 3, '0', STR_PAD_LEFT);
    }

    public function setValidationRulesForCreate()
    {
        $this->validationRules = [
            'tanggal' => 'required',
            'no_bon' => 'required',
            'kode_rute' => 'required',
        ];

        $this->validationMessages = [
            'tanggal' => ['required' => 'Tanggal tidak boleh kosong'],
            'no_bon' => ['required' => 'No Bon tidak boleh kosong'],
            'kode_rute' => ['required' => 'Rute tidak boleh kosong'],
        ];
    }

    public function setValidationRulesForUpdate()
    {
        $this->validationRules = [];
    }

    protected function setValidationForCreate(array $data)
    {
        $this->setValidationRulesForCreate();
        return $data;
    }

    protected function setValidationForUpdate(array $data)
    {
        $this->setValidationRulesForUpdate();
        return $data;
    }
}

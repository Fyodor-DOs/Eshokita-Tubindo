<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanModel extends Model
{
    protected $table            = 'pengiriman';
    protected $primaryKey       = 'id_pengiriman';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanggal', 'no_bon', 'supir', 'kenek', 'plat_kendaraan', 'kode_rute', 'id_customer',
        'nama_penerima', 'pembayaran', 'pemesanan', 'ttd_penerima', 'status',
        // file paths for documents
        'foto_surat_jalan', 'foto_penerimaan'
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
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setValidationForCreate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setValidationForUpdate'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generateNoBon()
    {
        $startNumber = 1;
        $formattedNumber = str_pad($startNumber, 6, '0', STR_PAD_LEFT); // Dimulai dari 000001

        do {
            $existingBon = $this->where('no_bon', $formattedNumber)->first();
            if ($existingBon) {
                $startNumber++;
                $formattedNumber = str_pad($startNumber, 6, '0', STR_PAD_LEFT);
            }
        } while ($existingBon);

        return $formattedNumber;
    }

    public function setValidationRulesForCreate()
    {
        $this->validationRules = [
            'tanggal' => 'required',
            'no_bon' => 'required',
            'kode_rute' => 'required',
        ];

        $this->validationMessages = [
            'tanggal' => [
                'required' => 'Tanggal tidak boleh kosong',
            ],
            'no_bon' => [
                'required' => 'No Bon tidak boleh kosong',
            ],
            'kode_rute' => [
                'required' => 'Rute tidak boleh kosong',
            ],
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

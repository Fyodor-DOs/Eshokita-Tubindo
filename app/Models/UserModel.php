<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'telepon', 'email', 'password', 'role'];

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
        'nama' => 'required',
        'telepon' => 'required|is_unique[user.telepon,id_user,{id_user}]',
        'email' => 'required|valid_email|is_unique[user.email,id_user,{id_user}]',
        'password' => 'required|min_length[8]',
    ];
    protected $validationMessages   = [
        'nama' => [
            'required' => 'Masukkan nama lengkap.'
        ],
        'telepon' => [
            'required' => 'Masukkan nomor telepon.',
            'is_unique' => 'Nomor telepon sudah terdaftar.'
        ],
        'email' => [
            'required' => 'Masukkan email.',
            'valid_email' => 'Email tidak valid.',
            'is_unique' => 'Email sudah terdaftar.'
        ],
        'password' => [
            'required' => 'Masukkan password.',
            'min_length' => 'Password minimal 8 karakter.'
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }
}

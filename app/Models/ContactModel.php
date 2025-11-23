<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table            = 'contacts';
    protected $primaryKey       = 'id_contact';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'telepon', 'email', 'alamat', 'note'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama' => 'required|min_length[2]',
        'telepon' => 'permit_empty|max_length[30]',
        'email' => 'permit_empty|valid_email',
    ];
    protected $validationMessages = [];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanModel extends Model
{
    protected $table            = 'penerimaan';
    protected $primaryKey       = 'id_penerimaan';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengiriman','id_customer','received_at','receiver_name','status','items_received','photo_path','note','verified','verified_by','verified_at','created_at','updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_pengiriman' => 'required|integer',
        'status' => 'in_list[received,partial,failed]',
    ];
}

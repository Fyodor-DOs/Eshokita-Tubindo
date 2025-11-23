<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'product';
    protected $primaryKey       = 'id_product';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sku', 'name', 'id_category', 'unit', 'active', 'price', 'qty', 'notes'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        // Allow same SKU on update for the same record using placeholder
        'sku'  => 'required|min_length[2]|is_unique[product.sku,id_product,{id_product}]',
        'name' => 'required|min_length[2]',
    ];
}

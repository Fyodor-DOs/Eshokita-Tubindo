<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $table            = 'product_category';
    protected $primaryKey       = 'id_category';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|is_unique[product_category.name,id_category,{id_category}]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Nama kategori wajib diisi',
            'min_length' => 'Nama kategori minimal 2 karakter',
            'is_unique' => 'Nama kategori sudah ada',
        ],
    ];
}

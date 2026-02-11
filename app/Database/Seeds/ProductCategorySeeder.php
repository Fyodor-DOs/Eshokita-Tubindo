<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\IdGenerator;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Serut', 'description' => 'Es serut untuk berbagai kebutuhan'],
            ['name' => 'Kristal Besar', 'description' => 'Kristal besar untuk pendinginan skala besar'],
            ['name' => 'Kristal Kecil', 'description' => 'Kristal kecil untuk konsumsi harian']
        ];

        foreach ($categories as $category) {
            $category['id_category'] = IdGenerator::generateForTable('product_category', 'id_category');
            $this->db->table('product_category')->insert($category);
        }
        echo "✓ Product Category seeded (3 kategori)\n";
    }
}

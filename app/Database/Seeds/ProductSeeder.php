<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $serutCat = $this->db->table('product_category')->where('name', 'Serut')->get()->getRowArray();
        $kristalBesarCat = $this->db->table('product_category')->where('name', 'Kristal Besar')->get()->getRowArray();
        $kristalKecilCat = $this->db->table('product_category')->where('name', 'Kristal Kecil')->get()->getRowArray();

        $categories = [
            ['cat' => $serutCat, 'prefix' => 'SRT', 'name' => 'Serut'],
            ['cat' => $kristalBesarCat, 'prefix' => 'KRB', 'name' => 'Kristal Besar'],
            ['cat' => $kristalKecilCat, 'prefix' => 'KRK', 'name' => 'Kristal Kecil'],
        ];
    $berats = [10, 20];
    $hargas = [15000, 20000];

        $products = [];
        foreach ($categories as $catInfo) {
            foreach ($berats as $i => $berat) {
                $products[] = [
                    'sku' => $catInfo['prefix'] . '-' . $berat . 'KG',
                    'name' => $catInfo['name'] . ' ' . $berat . 'kg',
                    'id_category' => $catInfo['cat']['id_category'],
                    'unit' => $berat,
                    'price' => $hargas[$i],
                    'qty' => rand(20, 100), 
                    'active' => 1
                ];
            }
        }

        foreach ($products as $product) {
            $this->db->table('product')->insert($product);
        }
    echo "✓ Product seeded (6 products: Serut, Kristal Besar, Kristal Kecil x 2 berat dengan stok 20-100)\n";
    }
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RuteSeeder extends Seeder
{
    public function run()
    {
        $rutes = [
            ['kode_rute' => 'UTARA', 'nama_wilayah' => 'Jakarta Utara (Mangga Dua, MOI, Mall Artha Gading)'],
            ['kode_rute' => 'METRO-10', 'nama_wilayah' => 'Metro 10 (Kokas, Kota Kasablanka)'],
            ['kode_rute' => 'METRO-11', 'nama_wilayah' => 'Metro 11 (Kota Wisata, Cibubur)'],
            ['kode_rute' => 'METRO-12', 'nama_wilayah' => 'Metro 12 (Jaksel, JGC, Jagakarsa)'],
            ['kode_rute' => 'METRO-4', 'nama_wilayah' => 'Metro 4 (Pacific Place, SCBD, Sudirman)'],
            ['kode_rute' => 'TIMUR', 'nama_wilayah' => 'Jakarta Timur (Cakung, Cipinang, Jatinegara)'],
            ['kode_rute' => 'BARAT', 'nama_wilayah' => 'Jakarta Barat (Cengkareng, Kebon Jeruk, Taman Sari)'],
            ['kode_rute' => 'SELATAN', 'nama_wilayah' => 'Jakarta Selatan (Kebayoran, Cilandak, Pasar Minggu)'],
        ];
        
        foreach ($rutes as $rute) {
            $this->db->table('rute')->insert($rute);
        }
        echo "✓ Rute seeded (8 rute)\n";
    }
}

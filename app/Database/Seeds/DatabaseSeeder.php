<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\IdGenerator;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Truncate semua tabel
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $tables = ['payment', 'invoice', 'shipment_tracking', 'penerimaan', 'nota', 'pengiriman', 'transaction', 'customer', 'product', 'product_category', 'rute', 'user', 'contacts'];
        foreach ($tables as $t) {
            if ($this->db->tableExists($t)) {
                $this->db->query("TRUNCATE TABLE `{$t}`");
            }
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        echo "✓ Semua tabel di-truncate\n";

        // 2. Seed semua
        $this->call('UserSeeder');
        $this->call('RuteSeeder');
        $this->call('ProductCategorySeeder');
        $this->call('ProductSeeder');

        echo "\n✓ Selesai! Semua data sudah di-seed ulang dengan format ID baru.\n";
    }
}

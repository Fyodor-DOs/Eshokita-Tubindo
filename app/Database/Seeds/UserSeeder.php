<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\IdGenerator;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'nama' => 'Administrator',
                'telepon' => '081234567891',
                'email' => 'admin@eshokita.com',
                'password' => password_hash('12345678', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'Staff Produksi',
                'telepon' => '081234567892',
                'email' => 'produksi@eshokita.com',
                'password' => password_hash('12345678', PASSWORD_DEFAULT),
                'role' => 'produksi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'Staff Distributor',
                'telepon' => '081234567893',
                'email' => 'distributor@eshokita.com',
                'password' => password_hash('12345678', PASSWORD_DEFAULT),
                'role' => 'distributor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('user')->where('email', $user['email'])->get()->getRowArray();
            if (!$existing) {
                $user['id_user'] = IdGenerator::generateForTable('user', 'id_user');
                $this->db->table('user')->insert($user);
            }
        }
        echo "✓ Users seeded (3 users)\n";
    }
}

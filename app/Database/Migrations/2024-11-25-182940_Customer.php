<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customer extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'kode_rute' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 15
            ],
            'provinsi' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'kabupaten' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'kelurahan' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'kodepos' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'produk' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id_customer', true);
        $this->forge->addForeignKey('kode_rute', 'rute', 'kode_rute', 'CASCADE', 'CASCADE', 'rute_customer');
        $this->forge->createTable('customer');
    }

    public function down()
    {
        $this->forge->dropForeignKey('customer', 'rute_customer');
        $this->forge->dropTable('customer');
    }
}

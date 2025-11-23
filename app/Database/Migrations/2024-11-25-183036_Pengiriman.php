<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pengiriman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengiriman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'tanggal' => [
                'type' => 'DATETIME'
            ],
            'no_bon' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'supir' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'kenek' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'plat_kendaraan' => [
                'type' => 'VARCHAR',
                'constraint' => 15
            ],
            'kode_rute' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'nama_penerima' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'kredit', 'transfer'],
                'default' => 'cash'
            ],
            'pemesanan' => [
                'type' => 'TEXT'
            ],
            'ttd_penerima' => [
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

        $this->forge->addKey('id_pengiriman', true);
        $this->forge->addForeignKey('kode_rute', 'rute', 'kode_rute', 'CASCADE', 'CASCADE', 'rute_pengiriman');
        $this->forge->addForeignKey('id_customer', 'customer', 'id_customer', 'CASCADE', 'CASCADE', 'customer_pengiriman');
        $this->forge->createTable('pengiriman');
    }

    public function down()
    {
        $this->forge->dropForeignKey('pengiriman', 'rute_pengiriman');
        $this->forge->dropForeignKey('pengiriman', 'customer_pengiriman');
        $this->forge->dropTable('pengiriman');
    }
}

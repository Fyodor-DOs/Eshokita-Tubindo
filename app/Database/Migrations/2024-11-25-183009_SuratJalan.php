<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuratJalan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_surat_jalan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'tanggal' => [
                'type' => 'DATE'
            ],
            'kode_rute' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'muatan' => [
                'type' => 'TEXT',
            ],
            'ttd_produksi' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id_surat_jalan', true);
        $this->forge->addForeignKey('kode_rute', 'rute', 'kode_rute', 'CASCADE', 'CASCADE', 'rute_nota');
        $this->forge->createTable('nota');
    }

    public function down()
    {
        $this->forge->dropForeignKey('nota', 'rute_nota');
        $this->forge->dropTable('nota');
    }
}
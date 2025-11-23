<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSuratJalanAddReceiver extends Migration
{
    public function up()
    {
        // Add new columns to connect Surat Jalan with Pengiriman & Customer,
        // plus store receiver name/signature. Also allow ttd_produksi to be NULL.
        $this->forge->addColumn('surat_jalan', [
            'id_pengiriman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_surat_jalan',
            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_pengiriman',
            ],
            'nama_penerima' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'kode_rute',
            ],
            'ttd_penerima' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nama_penerima',
            ],
        ]);

        // Modify ttd_produksi to be nullable
        $this->forge->modifyColumn('surat_jalan', [
            'ttd_produksi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert ttd_produksi to NOT NULL (best-effort; some DBs ignore null=false for TEXT without default)
        $this->forge->modifyColumn('surat_jalan', [
            'ttd_produksi' => [
                'type' => 'TEXT',
                'null' => false,
            ],
        ]);

        $this->forge->dropColumn('surat_jalan', ['id_pengiriman', 'id_customer', 'nama_penerima', 'ttd_penerima']);
    }
}

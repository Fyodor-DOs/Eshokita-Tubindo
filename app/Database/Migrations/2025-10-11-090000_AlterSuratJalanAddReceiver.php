<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSuratJalanAddReceiver extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('nota');

        // Add new columns to connect Nota with Pengiriman & Customer,
        // plus store receiver name/signature. Only add if not already present.
        $columnsToAdd = [];

        if (!in_array('id_pengiriman', $fields)) {
            $columnsToAdd['id_pengiriman'] = [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'id_surat_jalan',
            ];
        }
        if (!in_array('id_customer', $fields)) {
            $columnsToAdd['id_customer'] = [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'id_pengiriman',
            ];
        }
        if (!in_array('nama_penerima', $fields)) {
            $columnsToAdd['nama_penerima'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'kode_rute',
            ];
        }
        if (!in_array('ttd_penerima', $fields)) {
            $columnsToAdd['ttd_penerima'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nama_penerima',
            ];
        }

        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('nota', $columnsToAdd);
        }

        // Modify ttd_produksi to be nullable
        if (in_array('ttd_produksi', $fields)) {
            $this->forge->modifyColumn('nota', [
                'ttd_produksi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        // Revert ttd_produksi to NOT NULL (best-effort; some DBs ignore null=false for TEXT without default)
        $this->forge->modifyColumn('nota', [
            'ttd_produksi' => [
                'type' => 'TEXT',
                'null' => false,
            ],
        ]);

        $this->forge->dropColumn('nota', ['id_pengiriman', 'id_customer', 'nama_penerima', 'ttd_penerima']);
    }
}

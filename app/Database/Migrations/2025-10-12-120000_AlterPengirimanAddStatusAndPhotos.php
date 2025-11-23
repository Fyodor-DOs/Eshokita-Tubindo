<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPengirimanAddStatusAndPhotos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengiriman', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['siap', 'mengirim', 'diterima', 'gagal'],
                'default' => 'siap',
                'after' => 'ttd_penerima',
            ],
            'foto_surat_jalan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'status',
            ],
            'foto_penerimaan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'foto_surat_jalan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengiriman', ['status', 'foto_surat_jalan', 'foto_penerimaan']);
    }
}

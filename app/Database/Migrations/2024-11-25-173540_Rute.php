<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Rute extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rute' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'kode_rute' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true,
            ],
            'nama_wilayah' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id_rute', true);
        $this->forge->createTable('rute');
    }

    public function down()
    {
        $this->forge->dropTable('rute');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Penerimaan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penerimaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pengiriman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'received_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'receiver_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['received','partial','failed'],
                'default' => 'received',
            ],
            'items_received' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'photo_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_penerimaan', true);
        $this->forge->addKey(['id_pengiriman']);
        $this->forge->addForeignKey('id_pengiriman', 'pengiriman', 'id_pengiriman', 'CASCADE', 'CASCADE', 'fk_penerimaan_pengiriman');
        $this->forge->createTable('penerimaan');
    }

    public function down()
    {
        $this->forge->dropForeignKey('penerimaan', 'fk_penerimaan_pengiriman');
        $this->forge->dropTable('penerimaan');
    }
}

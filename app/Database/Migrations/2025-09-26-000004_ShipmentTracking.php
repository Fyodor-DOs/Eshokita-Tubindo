<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShipmentTracking extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tracking' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pengiriman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['created', 'on-route', 'delivered', 'failed', 'returned'],
                'default' => 'created',
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_tracking', true);
        $this->forge->addKey(['id_pengiriman']);
        $this->forge->addForeignKey('id_pengiriman', 'pengiriman', 'id_pengiriman', 'CASCADE', 'CASCADE', 'fk_tracking_pengiriman');
        $this->forge->createTable('shipment_tracking');
    }

    public function down()
    {
        $this->forge->dropForeignKey('shipment_tracking', 'fk_tracking_pengiriman');
        $this->forge->dropTable('shipment_tracking');
    }
}

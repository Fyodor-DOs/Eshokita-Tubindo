<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockTables extends Migration
{
    public function up()
    {
        // Drop existing tables if exist
        $this->forge->dropTable('stock_transaction', true);
        $this->forge->dropTable('stock', true);

        // Create stock table
        $this->forge->addField([
            'id_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_product' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_stock', true);
        $this->forge->addForeignKey('id_product', 'product', 'id_product', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock');

        // Create stock_transaction table
        $this->forge->addField([
            'id_stock_tx' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_product' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['in', 'out'],
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ref_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'ref_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_stock_tx', true);
        $this->forge->addForeignKey('id_product', 'product', 'id_product', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_transaction');
    }

    public function down()
    {
        $this->forge->dropTable('stock_transaction', true);
        $this->forge->dropTable('stock', true);
    }
}

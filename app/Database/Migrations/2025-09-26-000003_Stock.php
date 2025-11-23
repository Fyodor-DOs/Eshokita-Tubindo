<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stock extends Migration
{
    public function up()
    {
        // Stock master per product
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
        $this->forge->addUniqueKey(['id_product']);
        $this->forge->addForeignKey('id_product', 'product', 'id_product', 'CASCADE', 'CASCADE', 'fk_stock_product');
        $this->forge->createTable('stock');

        // Stock transactions
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
                'constraint' => ['in', 'out', 'adjust'],
                'default' => 'in',
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
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
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_stock_tx', true);
        $this->forge->addKey(['id_product']);
        $this->forge->addForeignKey('id_product', 'product', 'id_product', 'CASCADE', 'CASCADE', 'fk_stocktx_product');
        $this->forge->createTable('stock_transaction');
    }

    public function down()
    {
        $this->forge->dropForeignKey('stock_transaction', 'fk_stocktx_product');
        $this->forge->dropTable('stock_transaction');
        $this->forge->dropForeignKey('stock', 'fk_stock_product');
        $this->forge->dropTable('stock');
    }
}

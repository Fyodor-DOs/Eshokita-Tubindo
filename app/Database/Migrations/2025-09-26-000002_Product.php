<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Product extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_product' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_category' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'unit',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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

        $this->forge->addKey('id_product', true);
        $this->forge->addForeignKey('id_category', 'product_category', 'id_category', 'SET NULL', 'CASCADE', 'fk_product_category');
        $this->forge->createTable('product');
    }

    public function down()
    {
        $this->forge->dropForeignKey('product', 'fk_product_category');
        $this->forge->dropTable('product');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SimplifyStockSystem extends Migration
{
    public function up()
    {
        // Drop tabel stock dengan force (tanpa foreign key check)
        $db = \Config\Database::connect();
        
        if ($db->tableExists('stock_transaction')) {
            $db->disableForeignKeyChecks();
            $this->forge->dropTable('stock_transaction', true);
            $db->enableForeignKeyChecks();
        }
        
        if ($db->tableExists('stock')) {
            $db->disableForeignKeyChecks();
            $this->forge->dropTable('stock', true);
            $db->enableForeignKeyChecks();
        }

        // Tambah kolom qty di product
        $fields = [
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'price'
            ]
        ];
        $this->forge->addColumn('product', $fields);
    }

    public function down()
    {
        // Hapus kolom qty dari product
        $this->forge->dropColumn('product', 'qty');

        // Recreate stock tables (reverse dari migration Stock.php)
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
}

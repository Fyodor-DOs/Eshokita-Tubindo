<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_transaction' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'transaction_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
            ],
            'items' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'cancelled'],
                'default' => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('id_transaction', true);
        $this->forge->addKey(['id_customer']);
        $this->forge->addForeignKey('id_customer', 'customer', 'id_customer', 'CASCADE', 'CASCADE', 'fk_transaction_customer');
        $this->forge->createTable('transaction');
    }

    public function down()
    {
        $this->forge->dropForeignKey('transaction', 'fk_transaction_customer');
        $this->forge->dropTable('transaction');
    }
}

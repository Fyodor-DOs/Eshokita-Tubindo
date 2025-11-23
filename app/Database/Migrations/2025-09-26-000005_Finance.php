<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Finance extends Migration
{
    public function up()
    {
        // Invoices per pengiriman
        $this->forge->addField([
            'id_invoice' => [
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
            'invoice_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'issue_date' => [
                'type' => 'DATE',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'unpaid', 'partial', 'paid', 'void'],
                'default' => 'draft',
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
        $this->forge->addKey('id_invoice', true);
        $this->forge->addForeignKey('id_pengiriman', 'pengiriman', 'id_pengiriman', 'CASCADE', 'CASCADE', 'fk_invoice_pengiriman');
        $this->forge->createTable('invoice');

        // Payments linked to invoices
        $this->forge->addField([
            'id_payment' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_invoice' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
            ],
            'method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'transfer', 'other'],
                'default' => 'cash',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
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
        $this->forge->addKey('id_payment', true);
        $this->forge->addKey(['id_invoice']);
        $this->forge->addForeignKey('id_invoice', 'invoice', 'id_invoice', 'CASCADE', 'CASCADE', 'fk_payment_invoice');
        $this->forge->createTable('payment');
    }

    public function down()
    {
        $this->forge->dropForeignKey('payment', 'fk_payment_invoice');
        $this->forge->dropTable('payment');
        $this->forge->dropForeignKey('invoice', 'fk_invoice_pengiriman');
        $this->forge->dropTable('invoice');
    }
}

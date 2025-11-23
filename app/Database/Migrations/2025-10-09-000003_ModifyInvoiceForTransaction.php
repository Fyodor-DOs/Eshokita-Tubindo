<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyInvoiceForTransaction extends Migration
{
    public function up()
    {
        // Drop old foreign key
        $this->forge->dropForeignKey('invoice', 'fk_invoice_pengiriman');
        
        // Drop old column
        $this->forge->dropColumn('invoice', 'id_pengiriman');
        
        // Add new column
        $this->forge->addColumn('invoice', [
            'id_transaction' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id_invoice'
            ]
        ]);
        
        // Add new foreign key
        $this->db->query('ALTER TABLE invoice ADD CONSTRAINT fk_invoice_transaction FOREIGN KEY (id_transaction) REFERENCES transaction(id_transaction) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('invoice', 'fk_invoice_transaction');
        $this->forge->dropColumn('invoice', 'id_transaction');
        
        $this->forge->addColumn('invoice', [
            'id_pengiriman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id_invoice'
            ]
        ]);
        
        $this->db->query('ALTER TABLE invoice ADD CONSTRAINT fk_invoice_pengiriman FOREIGN KEY (id_pengiriman) REFERENCES pengiriman(id_pengiriman) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}

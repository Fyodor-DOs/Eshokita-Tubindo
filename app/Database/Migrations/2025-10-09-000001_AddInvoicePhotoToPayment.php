<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInvoicePhotoToPayment extends Migration
{
    public function up()
    {
        $this->forge->addColumn('payment', [
            'invoice_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'note'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('payment', 'invoice_photo');
    }
}

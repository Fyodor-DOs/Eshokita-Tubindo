<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKreditToPaymentMethod extends Migration
{
    public function up()
    {
        // Extend ENUM to include 'kredit'
        $fields = [
            'method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'kredit', 'transfer', 'other'],
                'default'    => 'cash',
            ],
        ];
        $this->forge->modifyColumn('payment', $fields);
    }

    public function down()
    {
        // Revert ENUM to previous set (without 'kredit')
        $fields = [
            'method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'transfer', 'other'],
                'default'    => 'cash',
            ],
        ];
        $this->forge->modifyColumn('payment', $fields);
    }
}

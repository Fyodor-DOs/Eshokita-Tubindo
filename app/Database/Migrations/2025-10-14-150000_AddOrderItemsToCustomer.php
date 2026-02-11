<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddOrderItemsToCustomer extends Migration
{
    public function up()
    {
        $fields = \Config\Database::connect()->getFieldNames('customer');
        if (!in_array('order_items', $fields)) {
            $this->forge->addColumn('customer', [
                'order_items' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'produk',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('customer', 'order_items');
    }
}

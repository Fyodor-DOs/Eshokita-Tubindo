<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransactionDedupeKey extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        // If column already exists, skip
        $fields = $db->getFieldNames('transaction');
        if (!in_array('dedupe_key', $fields)) {
            // Add column and unique index in a single ALTER
            $db->query("ALTER TABLE `transaction` ADD COLUMN `dedupe_key` VARCHAR(64) NULL AFTER `notes`");
            // Add unique index (allow NULLs, only non-null are enforced unique)
            $db->query("CREATE UNIQUE INDEX `uniq_transaction_dedupe` ON `transaction`(`dedupe_key`)");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('transaction');
        if (in_array('dedupe_key', $fields)) {
            // Drop unique index then drop column
            // Ignore errors if index name differs
            try { $db->query("DROP INDEX `uniq_transaction_dedupe` ON `transaction`"); } catch (\Throwable $th) {}
            $db->query("ALTER TABLE `transaction` DROP COLUMN `dedupe_key`");
        }
    }
}

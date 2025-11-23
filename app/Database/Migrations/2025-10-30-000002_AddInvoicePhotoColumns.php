<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInvoicePhotoColumns extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $driver = $db->DBDriver;

        $hasCol = function(string $table, string $column) use ($db, $driver): bool {
            try {
                if ($driver === 'MySQLi') {
                    $res = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
                    return ($res && $res->getNumRows() > 0);
                }
                if ($driver === 'SQLite3') {
                    $res = $db->query("PRAGMA table_info({$table})");
                    foreach ($res->getResultArray() as $r) {
                        if (strcasecmp($r['name'] ?? '', $column) === 0) return true;
                    }
                    return false;
                }
            } catch (\Throwable $e) {}
            return false;
        };

        if (!$hasCol('invoice', 'foto_surat_jalan')) {
            $forge->addColumn('invoice', [
                'foto_surat_jalan' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'status' ]
            ]);
        }
        if (!$hasCol('invoice', 'foto_penerimaan')) {
            $forge->addColumn('invoice', [
                'foto_penerimaan' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'foto_surat_jalan' ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        try { $forge->dropColumn('invoice', 'foto_penerimaan'); } catch (\Throwable $e) {}
        try { $forge->dropColumn('invoice', 'foto_surat_jalan'); } catch (\Throwable $e) {}
    }
}

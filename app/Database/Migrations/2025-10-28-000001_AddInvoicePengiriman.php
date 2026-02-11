<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInvoicePengiriman extends Migration
{
    private function columnExists(string $table, string $column): bool
    {
        try {
            // Works for MySQL/MariaDB
            $result = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            return (bool) ($result && $result->getNumRows());
        } catch (\Throwable $th) {
            // Best-effort fallback for other drivers
            try {
                $result = $this->db->query("PRAGMA table_info({$table})");
                if ($result) {
                    foreach ($result->getResultArray() as $row) {
                        if (isset($row['name']) && $row['name'] === $column)
                            return true;
                        if (isset($row['cid']) && isset($row['name']) && $row['name'] === $column)
                            return true;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        return false;
    }
    public function up()
    {
        // Tambah kolom id_pengiriman ke tabel invoice bila belum ada
        if (!$this->columnExists('invoice', 'id_pengiriman')) {
            $this->forge->addColumn('invoice', [
                'id_pengiriman' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
                    'default' => null,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->columnExists('invoice', 'id_pengiriman')) {
            $this->forge->dropColumn('invoice', 'id_pengiriman');
        }
    }
}

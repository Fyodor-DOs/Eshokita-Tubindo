<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * REVISI MASIF: Mengubah seluruh primary key dari INT auto_increment
 * menjadi VARCHAR(10) dengan format ID bermakna.
 *
 * Format ID: [KODE 3 digit][BULAN 2 digit][TAHUN 2 digit][URUT 3 digit]
 *
 * Juga mengoptimalkan panjang kolom agar tidak boros resource.
 */
class RevisiStrukturID extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // ===== 1. Matikan foreign key checks =====
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        // ===== 2. Truncate semua tabel (urutan child dulu) =====
        $tables = [
            'payment',
            'invoice',
            'shipment_tracking',
            'penerimaan',
            'nota',
            'pengiriman',
            'transaction',
            'customer',
            'product',
            'product_category',
            'rute',
            'user',
            'contacts',
        ];
        foreach ($tables as $t) {
            if ($db->tableExists($t)) {
                $db->query("TRUNCATE TABLE `{$t}`");
            }
        }

        // ===== 3. Drop semua foreign key constraints =====
        $this->safeDropFK('customer', 'rute_customer');
        $this->safeDropFK('nota', 'rute_nota');
        $this->safeDropFK('pengiriman', 'rute_pengiriman');
        $this->safeDropFK('pengiriman', 'customer_pengiriman');
        $this->safeDropFK('product', 'fk_product_category');
        $this->safeDropFK('shipment_tracking', 'fk_tracking_pengiriman');
        $this->safeDropFK('invoice', 'fk_invoice_transaction');
        $this->safeDropFK('invoice', 'fk_invoice_pengiriman');
        $this->safeDropFK('payment', 'fk_payment_invoice');
        $this->safeDropFK('transaction', 'fk_transaction_customer');
        $this->safeDropFK('penerimaan', 'fk_penerimaan_pengiriman');

        // ===== 4. Modify primary key columns =====

        // -- user --
        if ($db->tableExists('user')) {
            $db->query("ALTER TABLE `user` MODIFY `id_user` VARCHAR(10) NOT NULL");
        }

        // -- rute --
        if ($db->tableExists('rute')) {
            $db->query("ALTER TABLE `rute` MODIFY `id_rute` VARCHAR(10) NOT NULL");
        }

        // -- customer --
        if ($db->tableExists('customer')) {
            $db->query("ALTER TABLE `customer` MODIFY `id_customer` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `customer` MODIFY `kodepos` VARCHAR(5) NOT NULL");
        }

        // -- product_category --
        if ($db->tableExists('product_category')) {
            $db->query("ALTER TABLE `product_category` MODIFY `id_category` VARCHAR(10) NOT NULL");
        }

        // -- product --
        if ($db->tableExists('product')) {
            $db->query("ALTER TABLE `product` MODIFY `id_product` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `product` MODIFY `id_category` VARCHAR(10) NULL");
            $db->query("ALTER TABLE `product` MODIFY `qty` INT(7) NOT NULL DEFAULT 0");
        }

        // -- transaction --
        if ($db->tableExists('transaction')) {
            $db->query("ALTER TABLE `transaction` MODIFY `id_transaction` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `transaction` MODIFY `id_customer` VARCHAR(10) NOT NULL");
        }

        // -- pengiriman --
        if ($db->tableExists('pengiriman')) {
            $db->query("ALTER TABLE `pengiriman` MODIFY `id_pengiriman` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `pengiriman` MODIFY `id_customer` VARCHAR(10) NOT NULL");
        }

        // -- nota (surat_jalan) --
        if ($db->tableExists('nota')) {
            $db->query("ALTER TABLE `nota` MODIFY `id_surat_jalan` VARCHAR(10) NOT NULL");
            // Kolom tambahan dari AlterSuratJalanAddReceiver
            $this->safeModifyColumn('nota', 'id_pengiriman', 'VARCHAR(10) NULL');
            $this->safeModifyColumn('nota', 'id_customer', 'VARCHAR(10) NULL');
        }

        // -- shipment_tracking --
        if ($db->tableExists('shipment_tracking')) {
            $db->query("ALTER TABLE `shipment_tracking` MODIFY `id_tracking` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `shipment_tracking` MODIFY `id_pengiriman` VARCHAR(10) NOT NULL");
        }

        // -- invoice --
        if ($db->tableExists('invoice')) {
            $db->query("ALTER TABLE `invoice` MODIFY `id_invoice` VARCHAR(10) NOT NULL");
            // Pastikan id_transaction ada
            $this->safeAddOrModifyColumn('invoice', 'id_transaction', 'VARCHAR(10) NULL', 'id_invoice');
            // Pastikan id_pengiriman ada — drop dulu kalau INT lalu add ulang sebagai VARCHAR
            try {
                $colCheck = $db->query("SHOW COLUMNS FROM `invoice` LIKE 'id_pengiriman'");
                if ($colCheck && $colCheck->getNumRows() > 0) {
                    $colInfo = $colCheck->getRowArray();
                    if (stripos($colInfo['Type'], 'int') !== false) {
                        $db->query("ALTER TABLE `invoice` DROP COLUMN `id_pengiriman`");
                        $db->query("ALTER TABLE `invoice` ADD COLUMN `id_pengiriman` VARCHAR(10) NULL AFTER `id_transaction`");
                    } else {
                        $db->query("ALTER TABLE `invoice` MODIFY `id_pengiriman` VARCHAR(10) NULL");
                    }
                } else {
                    $db->query("ALTER TABLE `invoice` ADD COLUMN `id_pengiriman` VARCHAR(10) NULL AFTER `id_transaction`");
                }
            } catch (\Throwable $e) {
                log_message('warning', "invoice.id_pengiriman fix: " . $e->getMessage());
            }
        }

        // -- payment --
        if ($db->tableExists('payment')) {
            $db->query("ALTER TABLE `payment` MODIFY `id_payment` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `payment` MODIFY `id_invoice` VARCHAR(10) NOT NULL");
        }

        // -- penerimaan --
        if ($db->tableExists('penerimaan')) {
            $db->query("ALTER TABLE `penerimaan` MODIFY `id_penerimaan` VARCHAR(10) NOT NULL");
            $db->query("ALTER TABLE `penerimaan` MODIFY `id_pengiriman` VARCHAR(10) NOT NULL");
            $this->safeModifyColumn('penerimaan', 'id_customer', 'VARCHAR(10) NULL');
            $this->safeModifyColumn('penerimaan', 'verified_by', 'VARCHAR(10) NULL');
        }

        // -- contacts --
        if ($db->tableExists('contacts')) {
            $db->query("ALTER TABLE `contacts` MODIFY `id_contact` VARCHAR(10) NOT NULL");
        }

        // ===== 5. Re-add foreign keys =====

        // customer.kode_rute -> rute.kode_rute
        $this->safeAddFK('customer', 'kode_rute', 'rute', 'kode_rute', 'rute_customer');

        // pengiriman.kode_rute -> rute.kode_rute
        $this->safeAddFK('pengiriman', 'kode_rute', 'rute', 'kode_rute', 'rute_pengiriman');

        // pengiriman.id_customer -> customer.id_customer
        $this->safeAddFK('pengiriman', 'id_customer', 'customer', 'id_customer', 'customer_pengiriman');

        // nota.kode_rute -> rute.kode_rute
        $this->safeAddFK('nota', 'kode_rute', 'rute', 'kode_rute', 'rute_nota');

        // product.id_category -> product_category.id_category
        $this->safeAddFK('product', 'id_category', 'product_category', 'id_category', 'fk_product_category', 'SET NULL');

        // transaction.id_customer -> customer.id_customer
        $this->safeAddFK('transaction', 'id_customer', 'customer', 'id_customer', 'fk_transaction_customer');

        // shipment_tracking.id_pengiriman -> pengiriman.id_pengiriman
        $this->safeAddFK('shipment_tracking', 'id_pengiriman', 'pengiriman', 'id_pengiriman', 'fk_tracking_pengiriman');

        // invoice.id_transaction -> transaction.id_transaction (nullable)
        $this->safeAddFK('invoice', 'id_transaction', 'transaction', 'id_transaction', 'fk_invoice_transaction');

        // payment.id_invoice -> invoice.id_invoice
        $this->safeAddFK('payment', 'id_invoice', 'invoice', 'id_invoice', 'fk_payment_invoice');

        // penerimaan.id_pengiriman -> pengiriman.id_pengiriman
        $this->safeAddFK('penerimaan', 'id_pengiriman', 'pengiriman', 'id_pengiriman', 'fk_penerimaan_pengiriman');

        // ===== 6. Nyalakan kembali foreign key checks =====
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // Rollback tidak diperlukan — gunakan migrate:refresh untuk reset total.
        // Kolom sudah dibuat ulang oleh up().
    }

    // ===== Helper Methods =====

    private function safeDropFK(string $table, string $fkName): void
    {
        $db = \Config\Database::connect();
        try {
            if ($db->tableExists($table)) {
                $db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
            }
        } catch (\Throwable $e) {
            // FK mungkin sudah tidak ada, abaikan
        }
    }

    private function safeModifyColumn(string $table, string $column, string $definition): void
    {
        $db = \Config\Database::connect();
        try {
            $result = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            if ($result && $result->getNumRows() > 0) {
                $db->query("ALTER TABLE `{$table}` MODIFY `{$column}` {$definition}");
            }
        } catch (\Throwable $e) {
            // Kolom tidak ada, abaikan
        }
    }

    private function safeAddOrModifyColumn(string $table, string $column, string $definition, string $after = ''): void
    {
        $db = \Config\Database::connect();
        try {
            $result = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            $afterClause = $after ? " AFTER `{$after}`" : '';
            if ($result && $result->getNumRows() > 0) {
                $db->query("ALTER TABLE `{$table}` MODIFY `{$column}` {$definition}");
            } else {
                $db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}{$afterClause}");
            }
        } catch (\Throwable $e) {
            log_message('warning', "safeAddOrModify {$table}.{$column}: " . $e->getMessage());
        }
    }

    private function safeAddFK(string $table, string $col, string $refTable, string $refCol, string $fkName, string $onDelete = 'CASCADE'): void
    {
        $db = \Config\Database::connect();
        try {
            if ($db->tableExists($table) && $db->tableExists($refTable)) {
                $db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$col}`) REFERENCES `{$refTable}`(`{$refCol}`) ON DELETE {$onDelete} ON UPDATE CASCADE");
            }
        } catch (\Throwable $e) {
            log_message('warning', "Gagal add FK {$fkName}: " . $e->getMessage());
        }
    }
}

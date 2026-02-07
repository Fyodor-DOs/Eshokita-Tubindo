<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePaymentMethodEnum extends Migration
{
    public function up()
    {
        // Expand method ENUM to include all payment methods
        $this->db->query("ALTER TABLE payment MODIFY COLUMN method ENUM('cash','kredit','transfer','qris','va','ewallet','other') NOT NULL DEFAULT 'cash'");

        // Fix existing broken records where method is empty but note contains method info
        $this->db->query("UPDATE payment SET method = 'va' WHERE (method = '' OR method IS NULL) AND note LIKE '%Pembayaran via VA%'");
        $this->db->query("UPDATE payment SET method = 'ewallet' WHERE (method = '' OR method IS NULL) AND note LIKE '%Pembayaran via EWALLET%'");
        $this->db->query("UPDATE payment SET method = 'qris' WHERE (method = '' OR method IS NULL) AND note LIKE '%Pembayaran via QRIS%'");
        // Any remaining empty methods default to cash
        $this->db->query("UPDATE payment SET method = 'cash' WHERE method = '' OR method IS NULL");
    }

    public function down()
    {
        // Revert back to original ENUM (data loss possible)
        $this->db->query("UPDATE payment SET method = 'other' WHERE method IN ('qris','va','ewallet')");
        $this->db->query("ALTER TABLE payment MODIFY COLUMN method ENUM('cash','kredit','transfer','other') NOT NULL DEFAULT 'cash'");
    }
}

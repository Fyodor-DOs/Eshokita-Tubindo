<?php

namespace App\Libraries;

/**
 * ID Generator Library
 * 
 * Menghasilkan ID unik bermakna dengan format:
 * [TAHUN 2 digit][BULAN 2 digit][HARI 2 digit][NOMOR_URUT 3 digit]
 * 
 * Contoh: 260211001
 * - 26   = tahun (2026)
 * - 02   = bulan (Februari)
 * - 11   = hari (tanggal 11)
 * - 001  = nomor urut (reset setiap hari baru)
 * 
 * Total: 9 karakter → muat di VARCHAR(10)
 */
class IdGenerator
{
    /**
     * Generate ID bermakna.
     *
     * @param string $table      Nama tabel
     * @param string $primaryKey Nama kolom primary key
     * @return string ID baru (9 karakter)
     */
    public static function generate(string $table, string $primaryKey): string
    {
        $tahun = date('y'); // 2 digit terakhir tahun
        $bulan = date('m'); // 01-12
        $hari = date('d'); // 01-31

        // Prefix = tahun + bulan + hari => 6 karakter
        $prefix = $tahun . $bulan . $hari;

        $db = \Config\Database::connect();

        // Cari nomor urut terakhir dengan prefix yang sama (hari ini)
        $result = $db->table($table)
            ->select($primaryKey)
            ->like($primaryKey, $prefix, 'after')
            ->orderBy($primaryKey, 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($result) {
            $lastId = $result[$primaryKey];
            $lastSeq = (int) substr($lastId, strlen($prefix));
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        // Nomor urut 3 digit (001 - 999)
        return $prefix . str_pad((string) $newSeq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Shortcut: Generate ID berdasarkan nama tabel.
     * Sama dengan generate() — disimpan untuk kompatibilitas.
     *
     * @param string $table      Nama tabel
     * @param string $primaryKey Nama kolom primary key
     * @return string ID baru
     */
    public static function generateForTable(string $table, string $primaryKey): string
    {
        return self::generate($table, $primaryKey);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Stored Procedure: Nomor urut surat atomik per layanan per tahun
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_ambil_nomor_surat');
        DB::unprepared('
            CREATE PROCEDURE sp_ambil_nomor_surat(
                IN p_id_layanan SMALLINT UNSIGNED,
                IN p_tahun SMALLINT UNSIGNED,
                OUT p_urut INT UNSIGNED
            )
            BEGIN
                INSERT IGNORE INTO counter_surat (id_layanan, tahun, nomor_terakhir)
                VALUES (p_id_layanan, p_tahun, 0);

                SELECT nomor_terakhir + 1 INTO p_urut
                FROM counter_surat
                WHERE id_layanan = p_id_layanan AND tahun = p_tahun
                FOR UPDATE;

                UPDATE counter_surat SET nomor_terakhir = p_urut
                WHERE id_layanan = p_id_layanan AND tahun = p_tahun;
            END
        ');

        // 2. Stored Procedure: Rekap per layanan untuk periode tertentu
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rekap_periode');
        DB::unprepared("
            CREATE PROCEDURE sp_rekap_periode(IN p_dari DATE, IN p_sampai DATE)
            BEGIN
                SELECT l.kode, l.nama_layanan,
                       COUNT(p.id_pengajuan) AS total,
                       COALESCE(SUM(p.status = 'selesai'), 0) AS selesai,
                       COALESCE(SUM(p.status = 'ditolak'), 0) AS ditolak,
                       COALESCE(SUM(p.status IN ('menunggu','kurang_berkas','diproses','menunggu_ttd')), 0) AS berjalan,
                       ROUND(AVG(TIMESTAMPDIFF(HOUR, p.tgl_pengajuan, p.tgl_selesai)), 1) AS rata_jam_selesai
                FROM layanan l
                LEFT JOIN pengajuan p
                       ON p.id_layanan = l.id_layanan
                      AND p.deleted_at IS NULL
                      AND p.tgl_pengajuan >= p_dari
                      AND p.tgl_pengajuan <  p_sampai + INTERVAL 1 DAY
                GROUP BY l.id_layanan, l.kode, l.nama_layanan, l.urutan
                ORDER BY l.urutan;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rekap_periode');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_ambil_nomor_surat');
    }
};

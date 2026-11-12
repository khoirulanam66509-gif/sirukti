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
        // 1. View Antrean Pengajuan (tanpa pengajuan yang di-soft-delete)
        DB::unprepared("
            CREATE OR REPLACE VIEW v_antrean_pengajuan AS
            SELECT p.id_pengajuan, p.no_pengajuan, p.status, p.tgl_pengajuan,
                   w.nik, pg.nama AS nama_warga, pg.no_hp,
                   l.kode AS kode_layanan, l.nama_layanan,
                   (SELECT COUNT(*) FROM berkas_pengajuan b
                     WHERE b.id_pengajuan = p.id_pengajuan AND b.status_verifikasi = 'menunggu')        AS berkas_menunggu,
                   (SELECT COUNT(*) FROM berkas_pengajuan b
                     WHERE b.id_pengajuan = p.id_pengajuan AND b.status_verifikasi = 'perlu_perbaikan') AS berkas_perbaikan
            FROM pengajuan p
            JOIN warga w    ON w.id_warga = p.id_warga
            JOIN pengguna pg ON pg.id_pengguna = w.id_pengguna
            JOIN layanan l  ON l.id_layanan = p.id_layanan
            WHERE p.deleted_at IS NULL
        ");

        // 2. View Rekap Bulanan
        DB::unprepared('
            CREATE OR REPLACE VIEW v_rekap_bulanan AS
            SELECT YEAR(p.tgl_pengajuan) AS tahun, MONTH(p.tgl_pengajuan) AS bulan,
                   p.id_layanan, p.status, COUNT(*) AS jumlah
            FROM pengajuan p
            WHERE p.deleted_at IS NULL
            GROUP BY YEAR(p.tgl_pengajuan), MONTH(p.tgl_pengajuan), p.id_layanan, p.status
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_rekap_bulanan');
        DB::unprepared('DROP VIEW IF EXISTS v_antrean_pengajuan');
    }
};

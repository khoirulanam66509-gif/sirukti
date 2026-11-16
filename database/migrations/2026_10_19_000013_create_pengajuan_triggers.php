<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // 1. Trigger AFTER INSERT: Mencatat pembuatan pengajuan
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_ai');
            DB::unprepared("
                CREATE TRIGGER trg_pengajuan_ai AFTER INSERT ON pengajuan
                FOR EACH ROW
                BEGIN
                    INSERT INTO status_log (id_pengajuan, status_lama, status_baru, id_pengguna, catatan)
                    VALUES (NEW.id_pengajuan, NULL, NEW.status, NEW.id_pengubah, 'Pengajuan dibuat');
                END
            ");

            // 2. Trigger BEFORE UPDATE: Menjaga status final dan mengisi tanggal otomatis
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_bu');
            DB::unprepared("
                CREATE TRIGGER trg_pengajuan_bu BEFORE UPDATE ON pengajuan
                FOR EACH ROW
                BEGIN
                    IF OLD.status IN ('selesai','ditolak') AND NEW.status <> OLD.status THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Pengajuan berstatus final tidak dapat diubah statusnya';
                    END IF;
                    IF NEW.status = 'diproses' AND OLD.status <> 'diproses' AND NEW.tgl_diproses IS NULL THEN
                        SET NEW.tgl_diproses = NOW();
                    END IF;
                    IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
                        SET NEW.tgl_selesai = NOW();
                    END IF;
                END
            ");

            // 3. Trigger AFTER UPDATE: Mencatat setiap perubahan status
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_au');
            DB::unprepared('
                CREATE TRIGGER trg_pengajuan_au AFTER UPDATE ON pengajuan
                FOR EACH ROW
                BEGIN
                    IF NEW.status <> OLD.status THEN
                        INSERT INTO status_log (id_pengajuan, status_lama, status_baru, id_pengguna, catatan)
                        VALUES (NEW.id_pengajuan, OLD.status, NEW.status, NEW.id_pengubah, NEW.catatan_petugas);
                    END IF;
                END
            ');
        } catch (QueryException $e) {
            // Shared cloud MySQL dengan binary logging aktif menolak CREATE TRIGGER tanpa hak SUPER (Error 1419).
            // Logika trigger tetap dijalankan identik melalui fallback event pada Model Pengajuan.
            if (! str_contains($e->getMessage(), '1419')) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_au');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_bu');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_pengajuan_ai');
        } catch (QueryException $e) {
            if (! str_contains($e->getMessage(), '1419')) {
                throw $e;
            }
        }
    }
};

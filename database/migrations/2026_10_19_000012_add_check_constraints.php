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
        DB::statement("ALTER TABLE warga ADD CONSTRAINT chk_warga_nik CHECK (nik REGEXP '^[0-9]{16}$')");
        DB::statement("ALTER TABLE warga ADD CONSTRAINT chk_warga_no_kk CHECK (no_kk IS NULL OR no_kk REGEXP '^[0-9]{16}$')");
        DB::statement('ALTER TABLE berkas_pengajuan ADD CONSTRAINT chk_berkas_ukuran CHECK (ukuran_kb > 0)');
        DB::statement('ALTER TABLE counter_surat ADD CONSTRAINT chk_counter_nomor CHECK (nomor_terakhir >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE warga DROP CONSTRAINT chk_warga_nik');
        DB::statement('ALTER TABLE warga DROP CONSTRAINT chk_warga_no_kk');
        DB::statement('ALTER TABLE berkas_pengajuan DROP CONSTRAINT chk_berkas_ukuran');
        DB::statement('ALTER TABLE counter_surat DROP CONSTRAINT chk_counter_nomor');
    }
};

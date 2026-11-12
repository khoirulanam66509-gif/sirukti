<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berkas_pengajuan', function (Blueprint $table) {
            $table->increments('id_berkas');
            $table->unsignedInteger('id_pengajuan');
            $table->unsignedInteger('id_syarat');
            $table->string('nama_file_asli', 150);
            $table->string('path_file', 255);
            $table->string('mime', 50);
            $table->unsignedInteger('ukuran_kb');
            $table->enum('status_verifikasi', ['menunggu', 'sesuai', 'perlu_perbaikan'])->default('menunggu');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('pengajuan')
                ->cascadeOnDelete();

            $table->foreign('id_syarat')
                ->references('id_syarat')
                ->on('syarat_layanan')
                ->restrictOnDelete();

            $table->unique(['id_pengajuan', 'id_syarat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_pengajuan');
    }
};

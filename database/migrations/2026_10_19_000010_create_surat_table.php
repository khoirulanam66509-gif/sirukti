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
        Schema::create('surat', function (Blueprint $table) {
            $table->increments('id_surat');
            $table->unsignedInteger('id_pengajuan')->unique();
            $table->string('no_surat', 50)->unique();
            $table->date('tgl_surat');
            $table->char('kode_verifikasi', 32)->unique();
            $table->string('file_pdf', 255);
            $table->unsignedInteger('id_penandatangan');
            $table->dateTime('ditandatangani_at');
            $table->timestamps();

            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('pengajuan')
                ->restrictOnDelete();

            $table->foreign('id_penandatangan')
                ->references('id_pengguna')
                ->on('pengguna')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};

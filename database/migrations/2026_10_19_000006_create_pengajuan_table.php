<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->string('no_pengajuan', 20)->unique();
            $table->unsignedInteger('id_warga');
            $table->unsignedSmallInteger('id_layanan');
            $table->dateTime('tgl_pengajuan')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd', 'selesai', 'ditolak'])->default('menunggu');
            $table->json('data_isian');
            $table->text('catatan_petugas')->nullable();
            $table->unsignedInteger('id_verifikator')->nullable();
            $table->unsignedInteger('id_pengubah')->nullable();
            $table->dateTime('tgl_diproses')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_warga')
                ->references('id_warga')
                ->on('warga')
                ->restrictOnDelete();

            $table->foreign('id_layanan')
                ->references('id_layanan')
                ->on('layanan')
                ->restrictOnDelete();

            $table->foreign('id_verifikator')
                ->references('id_pengguna')
                ->on('pengguna')
                ->nullOnDelete();

            $table->foreign('id_pengubah')
                ->references('id_pengguna')
                ->on('pengguna')
                ->nullOnDelete();

            $table->index(['status', 'tgl_pengajuan']);
            $table->index(['id_warga', 'tgl_pengajuan']);
            $table->index(['id_layanan', 'tgl_pengajuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};

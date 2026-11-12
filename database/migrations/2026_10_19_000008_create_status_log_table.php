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
        Schema::create('status_log', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->unsignedInteger('id_pengajuan');
            $table->enum('status_lama', ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd', 'selesai', 'ditolak'])->nullable();
            $table->enum('status_baru', ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd', 'selesai', 'ditolak']);
            $table->unsignedInteger('id_pengguna')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('pengajuan')
                ->cascadeOnDelete();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->nullOnDelete();

            $table->index(['id_pengajuan', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_log');
    }
};

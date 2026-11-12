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
        Schema::create('counter_surat', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_layanan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('nomor_terakhir')->default(0);

            $table->primary(['id_layanan', 'tahun']);

            $table->foreign('id_layanan')
                ->references('id_layanan')
                ->on('layanan')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_surat');
    }
};

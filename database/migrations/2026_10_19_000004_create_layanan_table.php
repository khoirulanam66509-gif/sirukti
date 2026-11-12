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
        Schema::create('layanan', function (Blueprint $table) {
            $table->smallIncrements('id_layanan');
            $table->string('kode', 6)->unique();
            $table->string('nama_layanan', 100);
            $table->string('deskripsi', 255)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->unsignedTinyInteger('urutan')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};

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
        Schema::create('syarat_layanan', function (Blueprint $table) {
            $table->increments('id_syarat');
            $table->unsignedSmallInteger('id_layanan');
            $table->string('nama_syarat', 100);
            $table->string('petunjuk', 255)->nullable();
            $table->boolean('wajib')->default(true);
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->boolean('status_aktif')->default(true);

            $table->foreign('id_layanan')
                ->references('id_layanan')
                ->on('layanan')
                ->cascadeOnDelete();

            $table->unique(['id_layanan', 'nama_syarat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syarat_layanan');
    }
};

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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->increments('id_pengguna');
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('no_hp', 20)->nullable();
            $table->enum('role', ['warga', 'kasi', 'kepala'])->default('warga');
            $table->string('jabatan', 60)->nullable();
            $table->string('nip', 25)->nullable();
            $table->string('file_ttd', 255)->nullable();
            $table->boolean('wajib_ganti_password')->default(false);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};

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
        Schema::create('warga', function (Blueprint $table) {
            $table->increments('id_warga');
            $table->unsignedInteger('id_pengguna')->unique();
            $table->char('nik', 16)->unique();
            $table->char('no_kk', 16)->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama', 20)->nullable();
            $table->enum('status_perkawinan', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])->nullable();
            $table->string('pekerjaan', 60)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->unsignedSmallInteger('id_dusun')->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();

            $table->foreign('id_dusun')
                ->references('id_dusun')
                ->on('dusun')
                ->restrictOnDelete();

            $table->index('id_dusun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};

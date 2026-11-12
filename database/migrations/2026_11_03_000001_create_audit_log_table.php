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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->bigIncrements('id_audit');
            $table->unsignedInteger('id_pengguna')->nullable();
            $table->string('aksi', 60);
            $table->string('modul', 50);
            $table->string('target_label', 150)->nullable();
            $table->json('detail_perubahan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->nullOnDelete();

            $table->index(['modul', 'created_at'], 'idx_audit_modul_waktu');
            $table->index(['id_pengguna', 'created_at'], 'idx_audit_pengguna_waktu');
            $table->index('created_at', 'idx_audit_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};

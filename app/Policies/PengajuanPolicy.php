<?php

namespace App\Policies;

use App\Models\Pengajuan;
use App\Models\Pengguna;

class PengajuanPolicy
{
    /**
     * Menentukan apakah pengguna dapat melihat data pengajuan tertentu.
     */
    public function view(Pengguna $user, Pengajuan $pengajuan): bool
    {
        // Petugas (kasi dan kepala) berhak melihat seluruh pengajuan
        if (in_array($user->role, ['kasi', 'kepala'], true)) {
            return true;
        }

        // Warga hanya berhak melihat pengajuan miliknya sendiri
        if ($user->role === 'warga' && $user->warga) {
            return $pengajuan->id_warga === $user->warga->id_warga;
        }

        return false;
    }

    /**
     * Menentukan apakah pengguna dapat melihat atau mengunduh berkas fisik pengajuan.
     */
    public function viewBerkas(Pengguna $user, Pengajuan $pengajuan): bool
    {
        return $this->view($user, $pengajuan);
    }

    /**
     * Menentukan apakah pengguna dapat mengunduh surat resmi hasil pengajuan.
     */
    public function downloadSurat(Pengguna $user, Pengajuan $pengajuan): bool
    {
        return $this->view($user, $pengajuan) && $pengajuan->status === 'selesai';
    }

    /**
     * Menentukan apakah warga berhak mengunggah ulang berkas perbaikan.
     */
    public function unggahUlang(Pengguna $user, Pengajuan $pengajuan): bool
    {
        return $user->role === 'warga'
            && $user->warga
            && $pengajuan->id_warga === $user->warga->id_warga
            && $pengajuan->status === 'kurang_berkas';
    }

    /**
     * Menentukan apakah petugas berhak memverifikasi pengajuan.
     */
    public function verifikasi(Pengguna $user, Pengajuan $pengajuan): bool
    {
        return $user->role === 'kasi' && ! in_array($pengajuan->status, ['selesai', 'ditolak'], true);
    }

    /**
     * Menentukan apakah warga berhak membatalkan pengajuan (hanya saat status 'menunggu').
     */
    public function batalkan(Pengguna $user, Pengajuan $pengajuan): bool
    {
        return $user->role === 'warga'
            && $user->warga
            && $pengajuan->id_warga === $user->warga->id_warga
            && $pengajuan->status === 'menunggu';
    }
}

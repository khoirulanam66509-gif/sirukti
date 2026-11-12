<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Surat;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    /**
     * Tampilkan hasil verifikasi keaslian surat digital melalui kode verifikasi QR.
     */
    public function show(string $kode): View
    {
        $surat = Surat::with([
            'pengajuan.warga.pengguna',
            'pengajuan.warga.dusun',
            'pengajuan.layanan',
            'penandatangan',
        ])
            ->where('kode_verifikasi', $kode)
            ->first();

        $kop = Pengaturan::ambil('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Merdeka No. 1, Kampung Rukti Harjo',
            'kode_pos' => '34155',
            'telepon' => '',
            'whatsapp' => '',
        ]);

        $suratCfg = Pengaturan::ambil('surat', [
            'kode_kampung' => 'RH',
            'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo',
        ]);

        // Dokumen sah jika data surat ditemukan dan pengajuan terkait berstatus 'selesai'
        $isValid = $surat !== null && $surat->pengajuan !== null && $surat->pengajuan->status === 'selesai';

        return view('verifikasi.show', [
            'kode' => $kode,
            'isValid' => $isValid,
            'surat' => $surat,
            'warga' => $surat?->pengajuan?->warga,
            'layanan' => $surat?->pengajuan?->layanan,
            'penandatangan' => $surat?->penandatangan,
            'kop' => $kop,
            'suratCfg' => $suratCfg,
        ]);
    }
}

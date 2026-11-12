<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LacakPengajuanController extends Controller
{
    /**
     * Menampilkan halaman pelacakan cepat status pengajuan surat warga.
     */
    public function index(Request $request): View
    {
        $nomor = trim((string) $request->query('no_pengajuan', ''));
        $pengajuan = null;
        $ditemukan = false;

        if ($nomor !== '') {
            $pengajuan = Pengajuan::with([
                'layanan',
                'warga.pengguna',
                'surat',
                'statusLogs' => fn ($q) => $q->orderBy('created_at', 'asc'),
            ])->where('no_pengajuan', $nomor)->first();

            $ditemukan = $pengajuan !== null;
        }

        $profilKampung = Pengaturan::ambil('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'whatsapp' => '6281234567890',
        ]);

        return view('lacak.index', compact('nomor', 'pengajuan', 'ditemukan', 'profilKampung'));
    }
}

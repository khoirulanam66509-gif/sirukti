<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Menampilkan Landing Page publik SIRUKTI (L1).
     */
    public function index(Request $request): View
    {
        $layanan = Layanan::with('syaratAktif')
            ->where('status_aktif', true)
            ->orderBy('urutan')
            ->get();

        $nomorLacak = trim((string) $request->query('no_pengajuan', ''));
        $hasilLacak = null;
        $lacakDitemukan = false;

        if ($nomorLacak !== '') {
            $hasilLacak = Pengajuan::with([
                'layanan',
                'warga.pengguna',
                'surat',
                'statusLogs' => fn ($q) => $q->orderBy('created_at', 'asc'),
            ])->where('no_pengajuan', $nomorLacak)->first();

            $lacakDitemukan = $hasilLacak !== null;
        }

        $profilKampung = Pengaturan::ambil('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Merdeka No. 1, Kampung Rukti Harjo',
            'kode_pos' => '34155',
            'telepon' => '081234567890',
            'whatsapp' => '6281234567890',
        ]);

        $jamLayanan = Pengaturan::ambil('jam_layanan', [
            'senin_kamis' => '08.00 - 15.00 WIB',
            'jumat' => '08.00 - 11.30 WIB',
            'catatan' => 'Pelayanan tatap muka dilayani pada jam kantor, permohonan daring aktif 24 jam.',
        ]);

        $statistik = Pengaturan::ambil('statistik_kampung', [
            'jiwa' => 5990,
            'kk' => 1780,
            'surat_terbit' => 128,
            'sumber' => 'Laporan Bulanan Kependudukan',
        ]);

        if (! isset($statistik['surat_terbit'])) {
            $totalSelesai = Pengajuan::where('status', 'selesai')->count();
            $statistik['surat_terbit'] = $totalSelesai > 0 ? $totalSelesai : 128;
        }

        return view('landing', compact('layanan', 'profilKampung', 'jamLayanan', 'statistik', 'nomorLacak', 'hasilLacak', 'lacakDitemukan'));
    }
}

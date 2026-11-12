<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Pengaturan;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan beranda (dashboard) sesuai dengan peran akun pengguna yang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'kasi') {
            return $this->kasiDashboard($request);
        }

        if ($user->role === 'kepala') {
            return $this->kepalaDashboard($request);
        }

        return $this->wargaDashboard($request);
    }

    /**
     * Layar W1: Beranda Warga.
     */
    protected function wargaDashboard(Request $request): View
    {
        $user = $request->user();
        $warga = $user->warga;

        $persentaseProfil = $warga ? $warga->persentaseKelengkapan() : 0;
        $isProfilLengkap = $warga ? $warga->profilLengkap() : false;

        $totalSuratSelesai = 0;
        $pengajuanAktif = null;
        $riwayatTerbaru = collect();

        if ($warga) {
            $totalSuratSelesai = Pengajuan::where('id_warga', $warga->id_warga)
                ->where('status', 'selesai')
                ->count();

            // Pengajuan yang sedang berjalan (prioritas yang belum selesai)
            $pengajuanAktif = Pengajuan::with(['layanan', 'berkas.syarat', 'log'])
                ->where('id_warga', $warga->id_warga)
                ->whereIn('status', ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd'])
                ->latest('tgl_pengajuan')
                ->first();

            // 3 riwayat pengajuan terbaru
            $riwayatTerbaru = Pengajuan::with(['layanan', 'surat'])
                ->where('id_warga', $warga->id_warga)
                ->latest('tgl_pengajuan')
                ->take(3)
                ->get();
        }

        $profilKampung = Pengaturan::ambil('profil_kampung', []);

        return view('dashboard.warga', [
            'user' => $user,
            'warga' => $warga,
            'persentaseProfil' => $persentaseProfil,
            'isProfilLengkap' => $isProfilLengkap,
            'totalSuratSelesai' => $totalSuratSelesai,
            'pengajuanAktif' => $pengajuanAktif,
            'riwayatTerbaru' => $riwayatTerbaru,
            'profilKampung' => $profilKampung,
        ]);
    }

    /**
     * Layar K1: Beranda Kasi Pelayanan.
     */
    protected function kasiDashboard(Request $request): View
    {
        $user = $request->user();

        // Agregasi jumlah status dalam satu query efisien
        $statusCounts = Pengajuan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $menungguVerifikasi = $statusCounts['menunggu'] ?? 0;
        $kurangBerkas = $statusCounts['kurang_berkas'] ?? 0;
        $diproses = $statusCounts['diproses'] ?? 0;
        $menungguTtd = $statusCounts['menunggu_ttd'] ?? 0;

        $startOfMonth = now()->startOfMonth();
        $selesaiBulanIni = Pengajuan::where('status', 'selesai')
            ->where('tgl_selesai', '>=', $startOfMonth)
            ->count();

        // 5 antrean surat terbaru yang membutuhkan tindakan verifikasi
        $antreanTerbaru = Pengajuan::with(['warga.pengguna', 'warga.dusun', 'layanan'])
            ->whereIn('status', ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd'])
            ->latest('tgl_pengajuan')
            ->take(5)
            ->get();

        return view('dashboard.kasi', [
            'user' => $user,
            'menungguVerifikasi' => $menungguVerifikasi,
            'kurangBerkas' => $kurangBerkas,
            'diproses' => $diproses,
            'menungguTtd' => $menungguTtd,
            'selesaiBulanIni' => $selesaiBulanIni,
            'antreanTerbaru' => $antreanTerbaru,
        ]);
    }

    /**
     * Layar P1: Beranda Kepala Kampung.
     */
    protected function kepalaDashboard(Request $request): View
    {
        $user = $request->user();

        $menungguTtd = Pengajuan::where('status', 'menunggu_ttd')->count();

        $startOfMonth = now()->startOfMonth();
        $selesaiBulanIni = Pengajuan::where('status', 'selesai')
            ->where('tgl_selesai', '>=', $startOfMonth)
            ->count();

        $totalTahunIni = Surat::whereYear('tgl_surat', now()->year)->count();

        // Daftar permohonan yang menunggu persetujuan TTD Kepala
        $daftarMenungguTtd = Pengajuan::with(['warga.pengguna', 'warga.dusun', 'layanan'])
            ->where('status', 'menunggu_ttd')
            ->oldest('tgl_pengajuan')
            ->take(5)
            ->get();

        return view('dashboard.kepala', [
            'user' => $user,
            'menungguTtd' => $menungguTtd,
            'selesaiBulanIni' => $selesaiBulanIni,
            'totalTahunIni' => $totalTahunIni,
            'daftarMenungguTtd' => $daftarMenungguTtd,
        ]);
    }
}

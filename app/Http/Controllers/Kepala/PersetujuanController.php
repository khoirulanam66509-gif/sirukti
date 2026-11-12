<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Surat;
use App\Services\SuratService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersetujuanController extends Controller
{
    /**
     * Menampilkan daftar permohonan surat yang menunggu persetujuan Kepala Kampung (Layar P1).
     */
    public function index(Request $request): View
    {
        $tabFilter = $request->query('tab', 'menunggu_ttd');
        if (! in_array($tabFilter, ['menunggu_ttd', 'selesai'], true)) {
            $tabFilter = 'menunggu_ttd';
        }

        $query = Pengajuan::with(['warga.pengguna', 'layanan', 'surat'])
            ->where('status', $tabFilter);

        // Statistik KPI Kepala Kampung
        $kpi = [
            'menunggu_ttd' => Pengajuan::where('status', 'menunggu_ttd')->count(),
            'selesai_bulan_ini' => Surat::whereMonth('tgl_surat', date('m'))
                ->whereYear('tgl_surat', date('Y'))
                ->count(),
        ];

        // Filter layanan
        $layananFilter = $request->query('layanan');
        if (! empty($layananFilter) && $layananFilter !== 'semua') {
            $query->whereHas('layanan', function ($q) use ($layananFilter) {
                $q->where('kode', $layananFilter);
            });
        }

        // Filter pencarian
        $cari = trim((string) $request->query('cari'));
        if (! empty($cari)) {
            $query->where(function ($q) use ($cari) {
                $q->where('no_pengajuan', 'like', "%{$cari}%")
                    ->orWhereHas('warga.pengguna', function ($qw) use ($cari) {
                        $qw->where('nama', 'like', "%{$cari}%")
                            ->orWhere('username', 'like', "%{$cari}%");
                    })
                    ->orWhereHas('warga', function ($qw) use ($cari) {
                        $qw->where('nik', 'like', "%{$cari}%");
                    });
            });
        }

        $persetujuan = ($tabFilter === 'selesai' ? $query->orderBy('updated_at', 'desc') : $query->orderBy('tgl_pengajuan', 'asc'))
            ->paginate(10)
            ->withQueryString();

        $daftarLayanan = Layanan::where('status_aktif', true)->orderBy('urutan')->get();

        return view('kepala.persetujuan.index', [
            'persetujuan' => $persetujuan,
            'kpi' => $kpi,
            'tabFilter' => $tabFilter,
            'layananFilter' => $layananFilter ?: 'semua',
            'cari' => $cari,
            'daftarLayanan' => $daftarLayanan,
            'user' => $request->user(),
        ]);
    }

    /**
     * Menampilkan detail permohonan, pratinjau surat PDF, dan tombol persetujuan (Layar P2).
     */
    public function show(Pengajuan $pengajuan, Request $request): View
    {
        $pengajuan->loadMissing([
            'warga.pengguna',
            'warga.dusun',
            'layanan.syarat',
            'berkas.syarat',
            'statusLogs.pengguna',
            'verifikator',
            'surat',
        ]);

        return view('kepala.persetujuan.show', [
            'pengajuan' => $pengajuan,
            'user' => $request->user(),
        ]);
    }

    /**
     * Mengalirkan streaming pratinjau PDF surat sebelum ditandatangani.
     */
    public function streamPreviewPdf(Pengajuan $pengajuan, SuratService $suratService, Request $request)
    {
        $kepala = $request->user();
        $isDraft = $pengajuan->status !== 'selesai';
        $pdf = $suratService->renderPdf($pengajuan, $pengajuan->surat, $isDraft, $kepala);

        return $pdf->stream("PREVIEW-{$pengajuan->no_pengajuan}.pdf");
    }

    /**
     * Menyetujui dan menandatangani permohonan surat secara digital.
     */
    public function setujui(Pengajuan $pengajuan, SuratService $suratService, Request $request): RedirectResponse
    {
        $kepala = $request->user();

        $surat = $suratService->setujui($pengajuan, $kepala);

        return redirect()->route('kepala.persetujuan.index')
            ->with('status', "Permohonan {$pengajuan->no_pengajuan} berhasil disetujui. Surat resmi nomor {$surat->no_surat} telah diterbitkan dan dapat diunduh pemohon.");
    }

    /**
     * Mengembalikan permohonan ke Kasi untuk dikoreksi kembali.
     */
    public function kembalikan(Pengajuan $pengajuan, SuratService $suratService, Request $request): RedirectResponse
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Catatan alasan pengembalian wajib diisi.',
            'catatan.max' => 'Catatan pengembalian maksimal 500 karakter.',
        ]);

        $kepala = $request->user();
        $catatan = $request->input('catatan');

        $suratService->kembalikan($pengajuan, $kepala, $catatan);

        return redirect()->route('kepala.persetujuan.index')
            ->with('status', "Permohonan {$pengajuan->no_pengajuan} telah dikembalikan ke Kasi Pelayanan dengan catatan revisi.");
    }
}

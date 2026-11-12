<?php

namespace App\Http\Controllers\Kasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasi\VerifikasiPengajuanRequest;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use App\Services\SuratService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Menampilkan antrean permohonan surat masuk (Layar K2).
     */
    public function index(Request $request): View
    {
        $query = DB::table('v_antrean_pengajuan');

        // Statistik KPI ringkas antrean
        $kpi = [
            'total' => DB::table('v_antrean_pengajuan')->count(),
            'menunggu' => DB::table('v_antrean_pengajuan')->where('status', 'menunggu')->count(),
            'kurang_berkas' => DB::table('v_antrean_pengajuan')->where('status', 'kurang_berkas')->count(),
            'diproses' => DB::table('v_antrean_pengajuan')->where('status', 'diproses')->count(),
            'menunggu_ttd' => DB::table('v_antrean_pengajuan')->where('status', 'menunggu_ttd')->count(),
        ];

        // Filter status
        $statusFilter = $request->query('status');
        if (! empty($statusFilter) && $statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        // Filter layanan
        $layananFilter = $request->query('layanan');
        if (! empty($layananFilter) && $layananFilter !== 'semua') {
            $query->where('kode_layanan', $layananFilter);
        }

        // Filter pencarian (No. Pengajuan, Nama Warga, NIK)
        $cari = trim((string) $request->query('cari'));
        if (! empty($cari)) {
            $cariEscaped = addcslashes($cari, '%_\\');
            $query->where(function ($q) use ($cariEscaped) {
                $q->where('no_pengajuan', 'like', "%{$cariEscaped}%")
                    ->orWhere('nama_warga', 'like', "%{$cariEscaped}%")
                    ->orWhere('nik', 'like', "%{$cariEscaped}%");
            });
        }

        $antrean = $query->orderByDesc('tgl_pengajuan')
            ->paginate(10)
            ->withQueryString();

        // Respon permintaan AJAX (fragmen partial antrean)
        if ($request->ajax()) {
            return view('kasi.pengajuan.partials.daftar-antrean', [
                'antrean' => $antrean,
                'cari' => $cari,
                'statusFilter' => $statusFilter ?: 'semua',
                'layananFilter' => $layananFilter ?: 'semua',
            ]);
        }

        $daftarLayanan = Layanan::where('status_aktif', true)->orderBy('urutan')->get();

        return view('kasi.pengajuan.index', [
            'antrean' => $antrean,
            'kpi' => $kpi,
            'statusFilter' => $statusFilter ?: 'semua',
            'layananFilter' => $layananFilter ?: 'semua',
            'cari' => $cari,
            'daftarLayanan' => $daftarLayanan,
        ]);
    }

    /**
     * Menampilkan detail permohonan surat dan berkas untuk verifikasi (Layar K3).
     */
    public function show(Pengajuan $pengajuan): View
    {
        $pengajuan->load([
            'warga.pengguna',
            'warga.dusun',
            'layanan.syarat',
            'berkas.syarat',
            'statusLogs.pengguna',
            'verifikator',
        ]);

        $konfigLayanan = config("layanan.{$pengajuan->layanan->kode}", []);

        return view('kasi.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'konfigLayanan' => $konfigLayanan,
        ]);
    }

    /**
     * Memproses verifikasi berkas dan perubahan status permohonan oleh Kasi.
     */
    public function verifikasi(
        VerifikasiPengajuanRequest $request,
        Pengajuan $pengajuan,
        PengajuanService $pengajuanService
    ): RedirectResponse {
        $aksi = $request->validated('aksi');
        $catatanPetugas = $request->validated('catatan_petugas');
        $verifikasiBerkas = $request->validated('berkas', []);

        $mapStatus = [
            'proses' => 'diproses',
            'minta_perbaikan' => 'kurang_berkas',
            'ajukan_ttd' => 'menunggu_ttd',
            'tolak' => 'ditolak',
        ];

        $statusBaru = $mapStatus[$aksi];

        $user = $request->user();

        $pengajuanService->ubahStatus(
            $pengajuan,
            $statusBaru,
            $user,
            $catatanPetugas,
            $verifikasiBerkas
        );

        $pesanSukses = match ($statusBaru) {
            'diproses' => "Permohonan {$pengajuan->no_pengajuan} berhasil diverifikasi dan sedang diproses.",
            'kurang_berkas' => "Permohonan {$pengajuan->no_pengajuan} dikembalikan ke pemohon untuk melengkapi perbaikan berkas.",
            'menunggu_ttd' => "Permohonan {$pengajuan->no_pengajuan} telah diajukan ke Kepala Kampung untuk penandatanganan.",
            'ditolak' => "Permohonan {$pengajuan->no_pengajuan} telah ditolak.",
            default => 'Status permohonan berhasil diperbarui.',
        };

        return redirect()->route('kasi.pengajuan.show', $pengajuan)
            ->with('status', $pesanSukses);
    }

    /**
     * Menampilkan halaman pratinjau draft surat resmi (Layar K4).
     */
    public function pratinjauDraft(Pengajuan $pengajuan): View
    {
        $pengajuan->loadMissing(['layanan', 'warga.pengguna', 'warga.dusun']);

        return view('kasi.pengajuan.draft', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Mengalirkan (streaming) PDF draft surat untuk tampilan pratinjau di browser.
     */
    public function streamDraftPdf(Pengajuan $pengajuan, SuratService $suratService)
    {
        $pdf = $suratService->renderPdf($pengajuan, null, true);

        return $pdf->stream("DRAFT-{$pengajuan->no_pengajuan}.pdf");
    }
}

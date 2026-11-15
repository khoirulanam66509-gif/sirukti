<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArsipLaporanController extends Controller
{
    /**
     * Daftar nama bulan bahasa Indonesia.
     */
    public static array $daftarBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Memeriksa otorisasi role pengguna (Kasi & Kepala).
     */
    protected function checkRole(Request $request): void
    {
        $role = $request->user()?->role;
        abort_if(! in_array($role, ['kasi', 'kepala']), 403, 'Akses terbatas untuk Petugas Kasi dan Kepala Kampung.');
    }

    /**
     * Memanggil Stored Procedure sp_rekap_periode dengan pemulihan otomatis bila rutin belum ada.
     */
    protected function ambilRekapPeriode(string $startDate, string $endDate): array
    {
        try {
            return DB::select('CALL sp_rekap_periode(?, ?)', [$startDate, $endDate]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '1305')) {
                $migration = require database_path('migrations/2026_10_19_000014_create_stored_procedures.php');
                $migration->up();

                return DB::select('CALL sp_rekap_periode(?, ?)', [$startDate, $endDate]);
            }

            throw $e;
        }
    }

    /**
     * Menampilkan Layar K8 / P5: Arsip dan Laporan.
     */
    public function index(Request $request): View
    {
        $this->checkRole($request);

        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }
        if ($tahun < 2020 || $tahun > 2099) {
            $tahun = (int) date('Y');
        }

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

        // Rekapitulasi per layanan menggunakan Stored Procedure sp_rekap_periode
        $rekapLayanan = $this->ambilRekapPeriode($startDate, $endDate);

        // Ringkasan KPI periode
        $totalPengajuan = (int) array_sum(array_column($rekapLayanan, 'total'));
        $totalSelesai = (int) array_sum(array_column($rekapLayanan, 'selesai'));
        $totalDitolak = (int) array_sum(array_column($rekapLayanan, 'ditolak'));
        $totalBerjalan = (int) array_sum(array_column($rekapLayanan, 'berjalan'));

        // Rata-rata jam penyelesaian pengajuan berstatus selesai
        $avgJamSelesai = Pengajuan::whereBetween('tgl_pengajuan', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->where('status', 'selesai')
            ->whereNotNull('tgl_selesai')
            ->selectRaw('ROUND(AVG(TIMESTAMPDIFF(HOUR, tgl_pengajuan, tgl_selesai)), 1) as avg_jam')
            ->value('avg_jam') ?? 0;

        // Query arsip pengajuan pada periode yang dipilih
        $query = Pengajuan::with(['warga.pengguna', 'layanan', 'surat', 'verifikator'])
            ->whereBetween('tgl_pengajuan', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($request->filled('id_layanan')) {
            $query->where('id_layanan', $request->input('id_layanan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($keyword) {
                $sub->where('no_pengajuan', 'like', "%{$keyword}%")
                    ->orWhereHas('surat', function ($sq) use ($keyword) {
                        $sq->where('no_surat', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('warga', function ($wq) use ($keyword) {
                        $wq->where('nik', 'like', "%{$keyword}%")
                            ->orWhereHas('pengguna', function ($pq) use ($keyword) {
                                $pq->where('nama', 'like', "%{$keyword}%");
                            });
                    });
            });
        }

        $pengajuans = $query->orderByDesc('tgl_pengajuan')->paginate(15)->withQueryString();
        $layananList = Layanan::where('status_aktif', true)->orderBy('urutan')->get();

        $auditLogs = Schema::hasTable('audit_log')
            ? AuditLog::with('pengguna')->latest('created_at')->limit(50)->get()
            : collect();

        return view('arsip.index', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => self::$daftarBulan[$bulan],
            'rekapLayanan' => $rekapLayanan,
            'kpi' => [
                'total' => $totalPengajuan,
                'selesai' => $totalSelesai,
                'ditolak' => $totalDitolak,
                'berjalan' => $totalBerjalan,
                'avg_jam' => (float) $avgJamSelesai,
            ],
            'pengajuans' => $pengajuans,
            'layananList' => $layananList,
            'auditLogs' => $auditLogs,
            'role' => $request->user()->role,
        ]);
    }

    /**
     * Ekspor data arsip & rekapitulasi bulanan ke format CSV ramah Microsoft Excel (O3).
     */
    public function eksporCsv(Request $request): StreamedResponse
    {
        $this->checkRole($request);

        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }
        if ($tahun < 2020 || $tahun > 2099) {
            $tahun = (int) date('Y');
        }

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
        $namaBulan = self::$daftarBulan[$bulan];

        $rekapLayanan = $this->ambilRekapPeriode($startDate, $endDate);

        $query = Pengajuan::with(['warga.pengguna', 'layanan', 'surat', 'verifikator'])
            ->whereBetween('tgl_pengajuan', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($request->filled('id_layanan')) {
            $query->where('id_layanan', $request->input('id_layanan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($keyword) {
                $sub->where('no_pengajuan', 'like', "%{$keyword}%")
                    ->orWhereHas('surat', function ($sq) use ($keyword) {
                        $sq->where('no_surat', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('warga', function ($wq) use ($keyword) {
                        $wq->where('nik', 'like', "%{$keyword}%")
                            ->orWhereHas('pengguna', function ($pq) use ($keyword) {
                                $pq->where('nama', 'like', "%{$keyword}%");
                            });
                    });
            });
        }

        $pengajuanList = $query->orderBy('tgl_pengajuan')->get();
        $filename = "Arsip_Laporan_SIRUKTI_{$tahun}_{$bulan}.csv";

        return response()->streamDownload(function () use ($pengajuanList, $rekapLayanan, $namaBulan, $tahun) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar Microsoft Excel langsung mengenali encoding karakter dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            // Bagian 1: Judul & Ringkasan Rekapitulasi Bulanan
            fputcsv($handle, ['LAPORAN ARSIP & REKAPITULASI PELAYANAN SURAT — KAMPUNG RUKTI HARJO']);
            fputcsv($handle, ["Periode: {$namaBulan} {$tahun}", 'Dicetak: '.now('Asia/Jakarta')->format('d/m/Y H:i').' WIB']);
            fputcsv($handle, []);

            fputcsv($handle, ['REKAPITULASI PER JENIS LAYANAN']);
            fputcsv($handle, ['Kode Layanan', 'Nama Layanan', 'Total Pengajuan', 'Selesai', 'Berjalan', 'Ditolak', 'Rata-rata Waktu Selesai (Jam)']);
            foreach ($rekapLayanan as $r) {
                fputcsv($handle, [
                    $this->sanitizeCsvValue($r->kode),
                    $this->sanitizeCsvValue($r->nama_layanan),
                    $r->total,
                    $r->selesai,
                    $r->berjalan,
                    $r->ditolak,
                    $r->rata_jam_selesai ?? '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['DAFTAR RINCIAN ARSIP PENGAJUAN SURAT']);
            fputcsv($handle, [
                'No',
                'No. Pengajuan',
                'No. Surat Resmi',
                'Kode Layanan',
                'Jenis Layanan',
                'Nama Pemohon',
                'NIK (Disamarkan)',
                'Tanggal Pengajuan',
                'Tanggal Selesai',
                'Status',
                'Petugas Verifikator',
            ]);

            foreach ($pengajuanList as $idx => $p) {
                fputcsv($handle, [
                    $idx + 1,
                    $this->sanitizeCsvValue($p->no_pengajuan),
                    $this->sanitizeCsvValue($p->surat?->no_surat ?? '-'),
                    $this->sanitizeCsvValue($p->layanan?->kode ?? '-'),
                    $this->sanitizeCsvValue($p->layanan?->nama_layanan ?? '-'),
                    $this->sanitizeCsvValue($p->warga?->pengguna?->nama ?? '-'),
                    $this->sanitizeCsvValue(samarkan_nik($p->warga?->nik)),
                    $p->tgl_pengajuan ? $p->tgl_pengajuan->format('d/m/Y H:i') : '-',
                    $p->tgl_selesai ? $p->tgl_selesai->format('d/m/Y H:i') : '-',
                    strtoupper(str_replace('_', ' ', $p->status)),
                    $this->sanitizeCsvValue($p->verifikator?->nama ?? '-'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Mencetak laporan arsip bulanan dalam format PDF (DomPDF).
     */
    public function cetakPdf(Request $request): Response
    {
        $this->checkRole($request);

        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }
        if ($tahun < 2020 || $tahun > 2099) {
            $tahun = (int) date('Y');
        }

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

        // Rekapitulasi layanan via Stored Procedure
        $rekapLayanan = $this->ambilRekapPeriode($startDate, $endDate);

        // Daftar seluruh pengajuan pada periode tersebut
        $query = Pengajuan::with(['warga.pengguna', 'layanan', 'surat'])
            ->whereBetween('tgl_pengajuan', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($request->filled('id_layanan')) {
            $query->where('id_layanan', $request->input('id_layanan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pengajuanList = $query->orderBy('tgl_pengajuan')->get();

        // Data pengaturan kampung untuk kop & info laporan
        $pengaturan = Pengaturan::pluck('nilai', 'kunci')->toArray();

        // Pejabat Kepala Kampung
        $kepala = Pengguna::where('role', 'kepala')->where('status_aktif', true)->first();
        $petugas = $request->user();

        $pdf = Pdf::loadView('arsip.laporan_pdf', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => self::$daftarBulan[$bulan],
            'rekapLayanan' => $rekapLayanan,
            'pengajuanList' => $pengajuanList,
            'pengaturan' => $pengaturan,
            'kepala' => $kepala,
            'petugas' => $petugas,
            'tglCetak' => Carbon::now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'portrait');

        $filename = "Laporan_Pelayanan_Kampung_{$tahun}_{$bulan}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Soft delete arsip pengajuan (Hanya Kasi Pelayanan).
     */
    public function destroy(Pengajuan $pengajuan, Request $request): RedirectResponse
    {
        abort_if($request->user()->role !== 'kasi', 403, 'Hanya Kasi Pelayanan yang berwenang menghapus arsip pengajuan.');

        $noPengajuan = $pengajuan->no_pengajuan;
        $idPengajuan = $pengajuan->id_pengajuan;
        $namaPemohon = $pengajuan->warga?->pengguna?->nama ?? 'Warga';
        $kodeLayanan = $pengajuan->layanan?->kode ?? 'Surat';

        $pengajuan->delete();

        AuditLogService::catat(
            'Menghapus Arsip Surat (Soft Delete)',
            'Arsip & Laporan',
            "Berkas #{$idPengajuan} ({$kodeLayanan}) — {$namaPemohon}",
            ['id_pengajuan' => $idPengajuan, 'layanan' => $kodeLayanan]
        );

        return redirect()->back()->with('status', "Arsip permohonan #{$noPengajuan} berhasil dipindahkan ke arsip terhapus (soft delete).");
    }

    /**
     * Mencegah CSV formula injection (DDE) pada nilai teks dinamis.
     */
    protected function sanitizeCsvValue(mixed $value): string
    {
        $text = (string) ($value ?? '');

        if ($text !== '' && $text !== '-' && in_array($text[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $text;
        }

        return $text;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\BerkasPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Services\BerkasService;
use App\Services\PengajuanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanController extends Controller
{
    public function __construct(
        protected PengajuanService $pengajuanService,
        protected BerkasService $berkasService
    ) {}

    /**
     * Menampilkan riwayat seluruh pengajuan milik warga yang login (Layar W4).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $warga = $user->warga;

        if (! $warga) {
            abort(403, 'Akses riwayat pengajuan hanya diperuntukkan bagi akun warga.');
        }

        // Hitung statistik ringkasan KPI untuk 4 kartu mini
        $baseQuery = Pengajuan::where('id_warga', $warga->id_warga);

        $counts = [
            'total' => (clone $baseQuery)->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
            'berjalan' => (clone $baseQuery)->whereIn('status', ['menunggu', 'diproses', 'menunggu_ttd'])->count(),
            'perlu_tindakan' => (clone $baseQuery)->where('status', 'kurang_berkas')->count(),
            'menunggu' => (clone $baseQuery)->where('status', 'menunggu')->count(),
            'diproses' => (clone $baseQuery)->where('status', 'diproses')->count(),
            'menunggu_ttd' => (clone $baseQuery)->where('status', 'menunggu_ttd')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
        ];

        // Query utama dengan filter dan pencarian
        $query = Pengajuan::where('id_warga', $warga->id_warga)
            ->with(['layanan', 'berkas', 'surat'])
            ->latest('tgl_pengajuan')
            ->latest('id_pengajuan');

        // Filter status chip
        $statusFilter = $request->query('status');
        if ($statusFilter && in_array($statusFilter, ['menunggu', 'kurang_berkas', 'diproses', 'menunggu_ttd', 'selesai', 'ditolak'], true)) {
            $query->where('status', $statusFilter);
        }

        // Pencarian nomor pengajuan, nomor surat resmi, atau nama/kode layanan (R1)
        if ($search = trim((string) $request->query('q'))) {
            $escaped = addcslashes($search, '%_\\');
            $query->where(function ($q) use ($escaped) {
                $q->where('no_pengajuan', 'like', "%{$escaped}%")
                    ->orWhereHas('surat', function ($s) use ($escaped) {
                        $s->where('no_surat', 'like', "%{$escaped}%");
                    })
                    ->orWhereHas('layanan', function ($l) use ($escaped) {
                        $l->where('nama_layanan', 'like', "%{$escaped}%")
                            ->orWhere('kode', 'like', "%{$escaped}%");
                    });
            });
        }

        // Pagination 10 item per halaman sesuai keputusan
        $daftarPengajuan = $query->paginate(10)->withQueryString();

        // Progressive enhancement: respon potongan HTML partial saat AJAX
        if ($request->ajax()) {
            return view('pengajuan.partials.daftar-riwayat', [
                'daftarPengajuan' => $daftarPengajuan,
                'searchQuery' => $search,
                'statusFilter' => $statusFilter,
            ]);
        }

        return view('pengajuan.index', [
            'user' => $user,
            'warga' => $warga,
            'daftarPengajuan' => $daftarPengajuan,
            'counts' => $counts,
            'statusFilter' => $statusFilter,
            'searchQuery' => $search,
        ]);
    }

    /**
     * Menampilkan antarmuka formulir pengajuan surat (Stepper 4 Langkah W3).
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $warga = $user->warga;

        // Ambil seluruh layanan aktif beserta syaratnya
        $daftarLayanan = Layanan::where('status_aktif', true)
            ->with(['syaratAktif'])
            ->orderBy('urutan')
            ->get();

        // Cari tahu layanan mana saja yang masih memiliki pengajuan aktif oleh warga ini
        $pengajuanAktif = Pengajuan::where('id_warga', $warga->id_warga)
            ->whereIn('status', PengajuanService::STATUS_AKTIF)
            ->get()
            ->keyBy('id_layanan');

        $alamatLengkap = trim("{$warga->alamat}, RT {$warga->rt} / RW {$warga->rw}, {$warga->dusun?->nama_dusun}", ', ');

        return view('pengajuan.create', [
            'user' => $user,
            'warga' => $warga,
            'daftarLayanan' => $daftarLayanan,
            'pengajuanAktif' => $pengajuanAktif,
            'alamatLengkap' => $alamatLengkap,
            'configLayanan' => config('layanan', []),
        ]);
    }

    /**
     * Menyimpan permohonan surat baru ke basis data beserta berkas persyaratannya.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $user = $request->user();
        $warga = $user->warga;

        $layanan = Layanan::findOrFail($request->input('id_layanan'));
        $dataIsian = $request->input('data_isian', []);
        $files = $request->file('berkas', []);

        $pengajuan = $this->pengajuanService->buat($warga, $layanan, $dataIsian, $files, $user);

        return redirect()->route('riwayat')
            ->with('success', "Permohonan {$layanan->nama_layanan} berhasil dikirim dengan nomor {$pengajuan->no_pengajuan}.");
    }

    /**
     * Menampilkan halaman detail pengajuan, timeline aktivitas, dan daftar berkas (Layar W5).
     */
    public function show(Pengajuan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        $pengajuan->load([
            'layanan',
            'warga.pengguna',
            'warga.dusun',
            'berkas.syarat',
            'log.pengguna',
            'surat',
        ]);

        return view('pengajuan.show', [
            'pengajuan' => $pengajuan,
            'warga' => $pengajuan->warga,
            'userPemohon' => $pengajuan->warga?->pengguna,
            'configLayanan' => config("layanan.{$pengajuan->layanan?->kode}", []),
        ]);
    }

    /**
     * Mengunggah ulang berkas yang berstatus 'perlu_perbaikan' dan mengirim ulang pengajuan.
     */
    public function unggahUlang(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('unggahUlang', $pengajuan);

        $request->validate([
            'berkas' => ['required', 'array'],
            'berkas.*' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
        ], [
            'berkas.required' => 'Harap lampirkan berkas perbaikan.',
            'berkas.*.required' => 'File berkas pengganti wajib dipilih.',
            'berkas.*.mimes' => 'Format berkas harus berupa JPG, PNG, atau PDF.',
            'berkas.*.max' => 'Ukuran berkas perbaikan maksimal 2 MB.',
        ]);

        $this->pengajuanService->unggahUlangBerkas($pengajuan, $request->file('berkas', []), $request->user());

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Berkas perbaikan berhasil dikirim ulang. Status permohonan kini kembali menunggu verifikasi petugas.');
    }

    /**
     * Mengunduh surat resmi PDF bila permohonan telah berstatus 'selesai'.
     */
    public function downloadSurat(Pengajuan $pengajuan): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        Gate::authorize('downloadSurat', $pengajuan);

        $surat = $pengajuan->surat;
        if (! $surat) {
            abort(404, 'Data surat resmi belum tercatat.');
        }

        if (Storage::disk('private')->exists($surat->file_pdf)) {
            return Storage::disk('private')->download($surat->file_pdf, "Surat_{$pengajuan->no_pengajuan}.pdf");
        }

        abort(404, 'File PDF surat resmi tidak ditemukan di penyimpanan server.');
    }

    /**
     * Mengakses pratinjau atau mengunduh berkas persyaratan privat secara aman.
     */
    public function showBerkas(Request $request, Pengajuan $pengajuan, BerkasPengajuan $berkas): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('viewBerkas', $pengajuan);

        if ($berkas->id_pengajuan !== $pengajuan->id_pengajuan) {
            abort(404, 'Berkas persyaratan tidak ditemukan pada pengajuan ini.');
        }

        return $this->berkasService->tanggapiBerkas($berkas, $request->boolean('download'));
    }

    /**
     * Membatalkan permohonan surat oleh warga (hanya saat status masih 'menunggu').
     */
    public function batalkan(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('batalkan', $pengajuan);

        $alasan = $request->input('alasan');
        $catatan = ! empty(trim((string) $alasan))
            ? 'Dibatalkan oleh pemohon: '.trim((string) $alasan)
            : 'Dibatalkan oleh pemohon';

        $pengajuan->update([
            'status' => 'ditolak',
            'id_pengubah' => $request->user()->id_pengguna,
            'catatan_petugas' => $catatan,
        ]);

        return redirect()->route('riwayat')
            ->with('success', "Permohonan {$pengajuan->no_pengajuan} telah berhasil dibatalkan.");
    }

    /**
     * Mengembalikan status terkini pengajuan dalam format JSON untuk pembaruan otomatis (polling ringan).
     */
    public function statusJson(Pengajuan $pengajuan): JsonResponse
    {
        Gate::authorize('view', $pengajuan);

        return response()->json([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'no_pengajuan' => $pengajuan->no_pengajuan,
            'status' => $pengajuan->status,
            'status_label' => match ($pengajuan->status) {
                'menunggu' => 'Menunggu Verifikasi',
                'kurang_berkas' => 'Kurang Berkas / Perlu Perbaikan',
                'diproses' => 'Sedang Diproses',
                'menunggu_ttd' => 'Menunggu TTD Kepala Kampung',
                'selesai' => 'Selesai & Terbit',
                'ditolak' => 'Ditolak / Dibatalkan',
                default => ucfirst($pengajuan->status),
            },
            'is_final' => in_array($pengajuan->status, ['selesai', 'ditolak'], true),
            'updated_at' => $pengajuan->updated_at?->toIso8601String(),
        ]);
    }
}

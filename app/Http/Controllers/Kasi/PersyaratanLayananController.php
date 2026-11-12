<?php

namespace App\Http\Controllers\Kasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasi\StoreSyaratRequest;
use App\Http\Requests\Kasi\UpdateSyaratRequest;
use App\Models\Layanan;
use App\Models\SyaratLayanan;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersyaratanLayananController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan persyaratan layanan surat.
     */
    public function index(Request $request): View
    {
        $daftarLayanan = Layanan::with(['syarat' => function ($q) {
            $q->withCount('berkas')->orderBy('urutan');
        }])->orderBy('urutan')->get();

        $selectedId = $request->input('layanan_id', $daftarLayanan->first()?->id_layanan);
        $layananAktif = $daftarLayanan->firstWhere('id_layanan', $selectedId) ?? $daftarLayanan->first();

        return view('kasi.layanan.index', [
            'daftarLayanan' => $daftarLayanan,
            'layananAktif' => $layananAktif,
        ]);
    }

    /**
     * Simpan butir persyaratan baru untuk layanan tertentu.
     */
    public function storeSyarat(StoreSyaratRequest $request, Layanan $layanan): RedirectResponse
    {
        $validated = $request->validated();
        $nextUrutan = $validated['urutan'] ?? (($layanan->syarat()->max('urutan') ?? 0) + 1);

        $syarat = $layanan->syarat()->create([
            'nama_syarat' => $validated['nama_syarat'],
            'petunjuk' => $validated['petunjuk'] ?? null,
            'wajib' => $request->boolean('wajib', true),
            'urutan' => $nextUrutan,
            'status_aktif' => $request->boolean('status_aktif', true),
        ]);

        AuditLogService::catat(
            'Menambah Persyaratan Layanan',
            'Persyaratan Layanan',
            "{$layanan->kode} — {$syarat->nama_syarat}",
            ['id_layanan' => $layanan->id_layanan, 'wajib' => $syarat->wajib]
        );

        return redirect()->route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan])
            ->with('success', "Persyaratan baru untuk {$layanan->nama_layanan} berhasil ditambahkan.");
    }

    /**
     * Perbarui konfigurasi butir persyaratan.
     */
    public function updateSyarat(UpdateSyaratRequest $request, Layanan $layanan, SyaratLayanan $syarat): RedirectResponse
    {
        $validated = $request->validated();

        $syarat->update([
            'nama_syarat' => $validated['nama_syarat'],
            'petunjuk' => $validated['petunjuk'] ?? null,
            'wajib' => $request->boolean('wajib', false),
            'urutan' => $validated['urutan'] ?? $syarat->urutan,
            'status_aktif' => $request->boolean('status_aktif', false),
        ]);

        AuditLogService::catat(
            'Memperbarui Persyaratan Layanan',
            'Persyaratan Layanan',
            "{$layanan->kode} — {$syarat->nama_syarat}",
            ['id_syarat' => $syarat->id_syarat, 'wajib' => $syarat->wajib, 'status_aktif' => $syarat->status_aktif]
        );

        return redirect()->route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan])
            ->with('success', "Persyaratan '{$syarat->nama_syarat}' berhasil diperbarui.");
    }

    /**
     * Hapus butir persyaratan jika belum pernah digunakan dalam berkas pengajuan.
     */
    public function destroySyarat(Layanan $layanan, SyaratLayanan $syarat): RedirectResponse
    {
        // Pengecekan integritas: syarat yang sudah dipakai berkas tidak boleh dihapus
        if ($syarat->berkas()->exists()) {
            return redirect()->route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan])
                ->with('error', "Persyaratan '{$syarat->nama_syarat}' sudah pernah digunakan dalam pengajuan warga dan tidak dapat dihapus. Anda hanya dapat menonaktifkannya.");
        }

        $namaSyarat = $syarat->nama_syarat;
        $idSyarat = $syarat->id_syarat;
        $syarat->delete();

        AuditLogService::catat(
            'Menghapus Persyaratan Layanan',
            'Persyaratan Layanan',
            "{$layanan->kode} — {$namaSyarat}",
            ['id_syarat' => $idSyarat]
        );

        return redirect()->route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan])
            ->with('success', "Persyaratan '{$namaSyarat}' berhasil dihapus.");
    }

    /**
     * Beralih status aktif/nonaktif persyaratan secara instan.
     */
    public function toggleStatusSyarat(Layanan $layanan, SyaratLayanan $syarat): RedirectResponse
    {
        $statusBaru = ! $syarat->status_aktif;
        $syarat->update(['status_aktif' => $statusBaru]);

        $label = $statusBaru ? 'diaktifkan' : 'dinonaktifkan';

        AuditLogService::catat(
            $statusBaru ? 'Mengaktifkan Syarat Layanan' : 'Menonaktifkan Syarat Layanan',
            'Persyaratan Layanan',
            "{$layanan->kode} — {$syarat->nama_syarat}",
            ['id_syarat' => $syarat->id_syarat, 'status_aktif' => $statusBaru]
        );

        return redirect()->route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan])
            ->with('success', "Status persyaratan '{$syarat->nama_syarat}' berhasil {$label}.");
    }
}

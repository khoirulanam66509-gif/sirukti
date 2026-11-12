<?php

namespace App\Http\Controllers\Kasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasi\UpdateWargaRequest;
use App\Models\Dusun;
use App\Models\Warga;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WargaController extends Controller
{
    /**
     * Tampilkan daftar seluruh warga terdaftar dengan pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $query = Warga::with(['pengguna', 'dusun']);

        // Pencarian NIK, Nama Warga, atau Username
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $qEscaped = addcslashes($q, '%_\\');
            $query->where(function ($sub) use ($qEscaped) {
                $sub->where('nik', 'like', "%{$qEscaped}%")
                    ->orWhereHas('pengguna', function ($p) use ($qEscaped) {
                        $p->where('nama', 'like', "%{$qEscaped}%")
                            ->orWhere('username', 'like', "%{$qEscaped}%");
                    });
            });
        }

        // Filter Dusun
        if ($request->filled('dusun') && $request->input('dusun') !== '') {
            $query->where('id_dusun', $request->input('dusun'));
        }

        // Filter Status Akun
        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $status = $request->input('status');
            if ($status === 'aktif') {
                $query->whereHas('pengguna', fn ($p) => $p->where('status_aktif', true));
            } elseif ($status === 'nonaktif') {
                $query->whereHas('pengguna', fn ($p) => $p->where('status_aktif', false));
            } elseif ($status === 'reset') {
                $query->whereHas('pengguna', fn ($p) => $p->where('wajib_ganti_password', true));
            }
        }

        $wargaList = $query->latest('id_warga')->paginate(15)->withQueryString();

        // Respon permintaan AJAX (fragmen partial daftar warga)
        if ($request->ajax()) {
            return view('kasi.warga.partials.daftar-warga', [
                'wargaList' => $wargaList,
                'q' => $request->input('q', ''),
                'dusunSelected' => $request->input('dusun', ''),
                'statusSelected' => $request->input('status', 'semua'),
            ]);
        }

        // Statistik Cepat (KPI)
        $totalWarga = Warga::count();
        $totalAktif = Warga::whereHas('pengguna', fn ($p) => $p->where('status_aktif', true))->count();
        $totalReset = Warga::whereHas('pengguna', fn ($p) => $p->where('wajib_ganti_password', true))->count();
        $daftarDusun = Dusun::orderBy('nama_dusun')->get();

        return view('kasi.warga.index', [
            'wargaList' => $wargaList,
            'totalWarga' => $totalWarga,
            'totalAktif' => $totalAktif,
            'totalReset' => $totalReset,
            'daftarDusun' => $daftarDusun,
            'q' => $request->input('q', ''),
            'dusunSelected' => $request->input('dusun', ''),
            'statusSelected' => $request->input('status', 'semua'),
        ]);
    }

    /**
     * Tampilkan formulir lihat / koreksi data kependudukan warga.
     */
    public function edit(Warga $warga): View
    {
        $warga->load(['pengguna', 'dusun']);
        $daftarDusun = Dusun::orderBy('nama_dusun')->get();

        return view('kasi.warga.edit', [
            'warga' => $warga,
            'daftarDusun' => $daftarDusun,
        ]);
    }

    /**
     * Perbarui data warga dan akunnya.
     */
    public function update(UpdateWargaRequest $request, Warga $warga): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($warga, $validated) {
            // Update akun pengguna
            if ($warga->pengguna) {
                $dataPengguna = [
                    'nama' => $validated['nama'],
                    'no_hp' => $validated['no_hp'] ?? null,
                ];
                if (! empty($validated['username'])) {
                    $dataPengguna['username'] = Str::lower(trim($validated['username']));
                }
                $warga->pengguna->update($dataPengguna);
            }

            // Format RT dan RW
            $rt = ! empty($validated['rt']) ? str_pad(ltrim($validated['rt'], '0') ?: '0', 3, '0', STR_PAD_LEFT) : null;
            $rw = ! empty($validated['rw']) ? str_pad(ltrim($validated['rw'], '0') ?: '0', 3, '0', STR_PAD_LEFT) : null;

            // Update kependudukan warga
            $warga->update([
                'no_kk' => $validated['no_kk'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tgl_lahir' => $validated['tgl_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'agama' => $validated['agama'] ?? null,
                'status_perkawinan' => $validated['status_perkawinan'] ?? null,
                'pekerjaan' => $validated['pekerjaan'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'id_dusun' => $validated['id_dusun'] ?? null,
                'rt' => $rt,
                'rw' => $rw,
            ]);
        });

        AuditLogService::catat(
            'Memperbarui Data Warga',
            'Kelola Warga',
            $warga->pengguna?->nama ?? 'Warga #'.$warga->id_warga,
            ['id_warga' => $warga->id_warga, 'nik' => samarkan_nik($warga->nik)]
        );

        return redirect()->route('kasi.warga.index')->with('success', "Data warga {$warga->pengguna?->nama} berhasil diperbarui.");
    }

    /**
     * Reset password akun warga dengan password sementara dan paksa ganti saat login berikutnya.
     */
    public function resetPassword(Request $request, Warga $warga): RedirectResponse
    {
        $request->validate([
            'password_baru' => ['nullable', 'string', 'min:8'],
        ], [
            'password_baru.min' => 'Kata sandi sementara minimal 8 karakter.',
        ]);

        $passwordSementara = $request->filled('password_baru')
            ? $request->input('password_baru')
            : 'sirukti123';

        $pengguna = $warga->pengguna;
        if (! $pengguna) {
            return back()->with('error', 'Akun pengguna untuk warga ini tidak ditemukan.');
        }

        $pengguna->update([
            'password' => Hash::make($passwordSementara),
            'wajib_ganti_password' => true,
        ]);

        AuditLogService::catat(
            'Reset Kata Sandi Warga',
            'Kelola Warga',
            $pengguna->nama,
            ['id_warga' => $warga->id_warga, 'wajib_ganti_password' => true]
        );

        return redirect()->route('kasi.warga.index')->with('success', "Kata sandi untuk {$pengguna->nama} berhasil direset menjadi: {$passwordSementara} (Warga wajib mengganti sandi saat login).");
    }

    /**
     * Aktifkan atau nonaktifkan status akun warga.
     */
    public function toggleStatus(Warga $warga): RedirectResponse
    {
        $pengguna = $warga->pengguna;
        if (! $pengguna) {
            return back()->with('error', 'Akun pengguna tidak ditemukan.');
        }

        $statusBaru = ! $pengguna->status_aktif;
        $pengguna->update(['status_aktif' => $statusBaru]);

        $label = $statusBaru ? 'diaktifkan' : 'dinonaktifkan';

        AuditLogService::catat(
            $statusBaru ? 'Mengaktifkan Akun Warga' : 'Menonaktifkan Akun Warga',
            'Kelola Warga',
            $pengguna->nama,
            ['id_warga' => $warga->id_warga, 'status_aktif' => $statusBaru]
        );

        return redirect()->route('kasi.warga.index')->with('success', "Akun warga {$pengguna->nama} berhasil {$label}.");
    }
}

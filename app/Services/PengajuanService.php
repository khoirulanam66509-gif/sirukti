<?php

namespace App\Services;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengajuanService
{
    public function __construct(
        protected BerkasService $berkasService
    ) {}

    /**
     * Daftar status yang tergolong dalam pengajuan aktif (belum final).
     */
    public const STATUS_AKTIF = [
        'menunggu',
        'kurang_berkas',
        'diproses',
        'menunggu_ttd',
    ];

    /**
     * Memeriksa apakah warga memiliki pengajuan aktif pada layanan tertentu.
     */
    public function memilikiPengajuanAktif(int $idWarga, int $idLayanan): bool
    {
        return Pengajuan::where('id_warga', $idWarga)
            ->where('id_layanan', $idLayanan)
            ->whereIn('status', self::STATUS_AKTIF)
            ->exists();
    }

    /**
     * Menghasilkan nomor pengajuan berurutan format PG-YYYYMMDD-XXXX secara aman dan atomik.
     */
    public function buatNomorPengajuan(): string
    {
        $prefix = 'PG-'.now()->format('Ymd').'-';

        $lastPengajuan = Pengajuan::withTrashed()
            ->where('no_pengajuan', 'like', $prefix.'%')
            ->orderByDesc('no_pengajuan')
            ->lockForUpdate()
            ->value('no_pengajuan');

        $sequence = 1;
        if ($lastPengajuan) {
            $parts = explode('-', $lastPengajuan);
            $sequence = ((int) end($parts)) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Membuat pengajuan baru beserta berkas persyaratan dalam satu transaksi database.
     *
     * @param  array<string, mixed>  $dataIsian
     * @param  array<int, UploadedFile>  $files  [id_syarat => UploadedFile]
     *
     * @throws ValidationException
     */
    public function buat(Warga $warga, Layanan $layanan, array $dataIsian, array $files, Pengguna $user): Pengajuan
    {
        // 1. Pastikan profil warga sudah lengkap
        if (! $warga->profilLengkap()) {
            throw ValidationException::withMessages([
                'profil' => 'Profil kependudukan Anda belum lengkap. Silakan lengkapi profil terlebih dahulu.',
            ]);
        }

        // 2. Pastikan tidak ada pengajuan aktif ganda pada layanan yang sama
        if ($this->memilikiPengajuanAktif($warga->id_warga, $layanan->id_layanan)) {
            throw ValidationException::withMessages([
                'id_layanan' => "Anda masih memiliki permohonan {$layanan->nama_layanan} yang sedang berjalan. Permohonan baru baru dapat dibuat setelah pengajuan sebelumnya selesai atau ditolak.",
            ]);
        }

        // 3. Pastikan seluruh berkas persyaratan wajib terunggah
        $syaratWajib = $layanan->syaratAktif()->where('wajib', true)->get();
        foreach ($syaratWajib as $syarat) {
            if (empty($files[$syarat->id_syarat]) || ! ($files[$syarat->id_syarat] instanceof UploadedFile)) {
                throw ValidationException::withMessages([
                    "berkas.{$syarat->id_syarat}" => "Berkas persyaratan '{$syarat->nama_syarat}' wajib diunggah.",
                ]);
            }
        }

        $savedFilePaths = [];

        try {
            return DB::transaction(function () use ($warga, $layanan, $dataIsian, $files, $user, &$savedFilePaths) {
                // Generate nomor unik atomik
                $noPengajuan = $this->buatNomorPengajuan();

                // Simpan baris pengajuan (trigger trg_pengajuan_ai membaca id_pengubah untuk status_log awal)
                $pengajuan = Pengajuan::create([
                    'no_pengajuan' => $noPengajuan,
                    'id_warga' => $warga->id_warga,
                    'id_layanan' => $layanan->id_layanan,
                    'tgl_pengajuan' => now(),
                    'status' => 'menunggu',
                    'data_isian' => $dataIsian,
                    'catatan_petugas' => null,
                    'id_verifikator' => null,
                    'id_pengubah' => $user->id_pengguna,
                ]);

                // Simpan seluruh berkas fisik dan catat ke tabel berkas_pengajuan
                foreach ($files as $idSyarat => $file) {
                    if ($file instanceof UploadedFile) {
                        $berkas = $this->berkasService->simpanBerkas($file, $pengajuan->id_pengajuan, (int) $idSyarat);
                        $savedFilePaths[] = $berkas->path_file;
                    }
                }

                return $pengajuan;
            });
        } catch (Exception $e) {
            // Rollback fisik: Hapus seluruh file yang sempat tersimpan ke storage privat
            foreach ($savedFilePaths as $path) {
                $this->berkasService->hapusFileFisik($path);
            }

            throw $e;
        }
    }

    /**
     * Mengunggah ulang berkas yang berstatus 'perlu_perbaikan' dan memperbarui status pengajuan.
     *
     * @param  array<int, UploadedFile>  $files  [id_berkas => UploadedFile]
     *
     * @throws ValidationException
     */
    public function unggahUlangBerkas(Pengajuan $pengajuan, array $files, Pengguna $user): void
    {
        if ($pengajuan->status !== 'kurang_berkas') {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan ini tidak dalam status kurang berkas.',
            ]);
        }

        $berkasPerluPerbaikan = $pengajuan->berkas()
            ->where('status_verifikasi', 'perlu_perbaikan')
            ->get()
            ->keyBy('id_berkas');

        if ($berkasPerluPerbaikan->isEmpty()) {
            throw ValidationException::withMessages([
                'berkas' => 'Tidak ada berkas yang memerlukan perbaikan pada pengajuan ini.',
            ]);
        }

        // Pastikan hanya berkas yang berstatus 'perlu_perbaikan' yang dapat diunggah ulang
        foreach ($files as $idBerkas => $file) {
            if (! isset($berkasPerluPerbaikan[$idBerkas])) {
                throw ValidationException::withMessages([
                    "berkas.{$idBerkas}" => 'Hanya berkas yang berstatus perlu perbaikan yang dapat diunggah ulang.',
                ]);
            }
        }

        // Pastikan semua berkas yang berstatus 'perlu_perbaikan' telah disediakan penggantinya
        foreach ($berkasPerluPerbaikan as $idBerkas => $b) {
            if (empty($files[$idBerkas]) || ! ($files[$idBerkas] instanceof UploadedFile)) {
                $namaSyarat = $b->syarat?->nama_syarat ?? 'Persyaratan';
                throw ValidationException::withMessages([
                    "berkas.{$idBerkas}" => "Berkas '{$namaSyarat}' wajib dipilih berkas penggantinya.",
                ]);
            }
        }

        $oldPathsToDelete = [];
        $newPathsCreated = [];

        try {
            DB::transaction(function () use ($pengajuan, $files, $berkasPerluPerbaikan, $user, &$oldPathsToDelete, &$newPathsCreated) {
                foreach ($files as $idBerkas => $file) {
                    $berkas = $berkasPerluPerbaikan[$idBerkas];
                    $oldPathsToDelete[] = $berkas->path_file;

                    // Simpan berkas pengganti
                    $newBerkas = $this->berkasService->simpanBerkas($file, $pengajuan->id_pengajuan, $berkas->id_syarat);
                    $newPathsCreated[] = $newBerkas->path_file;
                }

                // Setelah seluruh berkas perbaikan diunggah dan statusnya kembali 'menunggu',
                // ubah status pengajuan dari kurang_berkas menjadi menunggu
                $pengajuan->update([
                    'status' => 'menunggu',
                    'id_pengubah' => $user->id_pengguna,
                    'catatan_petugas' => 'Berkas perbaikan telah diunggah ulang oleh pemohon',
                ]);
            });

            // Hapus berkas fisik lama setelah transaksi sukses
            foreach ($oldPathsToDelete as $oldPath) {
                $this->berkasService->hapusFileFisik($oldPath);
            }
        } catch (Exception $e) {
            // Hapus file baru jika transaksi gagal
            foreach ($newPathsCreated as $newPath) {
                $this->berkasService->hapusFileFisik($newPath);
            }

            throw $e;
        }
    }

    /**
     * Mengubah status pengajuan oleh petugas dengan validasi matriks perpindahan status
     * dan verifikasi berkas permohonan.
     *
     * @param  array<int, array{status_verifikasi: string, catatan?: ?string}>  $verifikasiBerkas  [id_berkas => [...]]
     *
     * @throws ValidationException
     */
    public function ubahStatus(
        Pengajuan $pengajuan,
        string $statusBaru,
        Pengguna $user,
        ?string $catatan = null,
        array $verifikasiBerkas = []
    ): Pengajuan {
        $statusLama = $pengajuan->status;

        // 1. Cek status final (selesai / ditolak tidak dapat diubah)
        if (in_array($statusLama, ['selesai', 'ditolak'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan berstatus final (selesai atau ditolak) tidak dapat diubah statusnya.',
            ]);
        }

        // 2. Matriks transisi status yang diizinkan untuk Kasi
        $matriksKasi = [
            'menunggu' => ['kurang_berkas', 'diproses', 'ditolak'],
            'kurang_berkas' => ['ditolak'],
            'diproses' => ['menunggu_ttd', 'kurang_berkas', 'ditolak'],
        ];

        if ($user->role === 'kasi') {
            $allowedTransitions = $matriksKasi[$statusLama] ?? [];
            if (! in_array($statusBaru, $allowedTransitions, true)) {
                throw ValidationException::withMessages([
                    'status' => "Perubahan status dari '{$statusLama}' ke '{$statusBaru}' tidak diizinkan untuk Kasi.",
                ]);
            }
        }

        // 3. Validasi khusus berdasarkan status tujuan
        if ($statusBaru === 'ditolak') {
            $catatanBersih = trim((string) $catatan);
            if (empty($catatanBersih)) {
                throw ValidationException::withMessages([
                    'catatan_petugas' => 'Alasan penolakan pengajuan wajib diisi.',
                ]);
            }
        }

        try {
            return DB::transaction(function () use ($pengajuan, $statusBaru, $user, $catatan, $verifikasiBerkas) {
                // Perbarui verifikasi berkas bila disertakan
                if (! empty($verifikasiBerkas)) {
                    foreach ($verifikasiBerkas as $idBerkas => $item) {
                        $statusVerifikasi = $item['status_verifikasi'] ?? 'menunggu';
                        $catatanBerkas = $item['catatan'] ?? null;

                        $pengajuan->berkas()
                            ->where('id_berkas', $idBerkas)
                            ->update([
                                'status_verifikasi' => $statusVerifikasi,
                                'catatan' => $statusVerifikasi === 'perlu_perbaikan' ? $catatanBerkas : null,
                            ]);
                    }
                }

                // Validasi kelengkapan berkas untuk status 'kurang_berkas'
                if ($statusBaru === 'kurang_berkas') {
                    $adaPerbaikan = $pengajuan->berkas()
                        ->where('status_verifikasi', 'perlu_perbaikan')
                        ->exists();

                    if (! $adaPerbaikan) {
                        throw ValidationException::withMessages([
                            'berkas' => 'Untuk meminta perbaikan berkas, minimal satu berkas harus ditandai Perlu Perbaikan beserta catatannya.',
                        ]);
                    }
                }

                // Validasi kelengkapan berkas untuk status 'diproses' dan 'menunggu_ttd'
                if (in_array($statusBaru, ['diproses', 'menunggu_ttd'], true)) {
                    $adaBelumSesuai = $pengajuan->berkas()
                        ->where('status_verifikasi', '!=', 'sesuai')
                        ->exists();

                    if ($adaBelumSesuai) {
                        throw ValidationException::withMessages([
                            'berkas' => 'Seluruh berkas persyaratan harus diverifikasi Sesuai sebelum pengajuan dapat diproses atau diajukan ke Kepala Kampung.',
                        ]);
                    }
                }

                // Update pengajuan (trigger trg_pengajuan_au akan mencatat perubahan ke status_log)
                $updateData = [
                    'status' => $statusBaru,
                    'id_pengubah' => $user->id_pengguna,
                    'catatan_petugas' => $catatan ?: null,
                ];

                // Jika Kasi yang melakukan verifikasi, simpan id_verifikator
                if ($user->role === 'kasi' && ! $pengajuan->id_verifikator) {
                    $updateData['id_verifikator'] = $user->id_pengguna;
                }

                $pengajuan->update($updateData);

                return $pengajuan->fresh();
            });
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '45000') || str_contains($e->getMessage(), 'Pengajuan berstatus final')) {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan berstatus final (selesai atau ditolak) tidak dapat diubah statusnya.',
                ]);
            }
            throw $e;
        }
    }
}

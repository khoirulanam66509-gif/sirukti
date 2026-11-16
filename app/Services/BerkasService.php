<?php

namespace App\Services;

use App\Models\BerkasPengajuan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BerkasService
{
    /**
     * MIME type yang diizinkan untuk berkas persyaratan.
     */
    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    /**
     * Batas ukuran maksimum berkas (dalam Kilobyte): 2 MB = 2048 KB.
     */
    public const MAX_SIZE_KB = 2048;

    /**
     * Memvalidasi berkas unggahan sebelum disimpan.
     *
     * @throws InvalidArgumentException
     */
    public function validasiBerkas(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Berkas yang diunggah mengalami kerusakan atau gagal terunggah.');
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new InvalidArgumentException("Format berkas ({$mime}) tidak didukung. Harap unggah berkas berformat JPG, PNG, atau PDF.");
        }

        $sizeKb = (int) ceil($file->getSize() / 1024);
        if ($sizeKb <= 0) {
            throw new InvalidArgumentException('Ukuran berkas tidak boleh kosong (0 KB).');
        }

        if ($sizeKb > self::MAX_SIZE_KB) {
            throw new InvalidArgumentException("Ukuran berkas ({$sizeKb} KB) melebihi batas maksimum 2048 KB (2 MB).");
        }
    }

    /**
     * Menyimpan berkas ke disk privat dan mencatatnya ke database.
     */
    public function simpanBerkas(UploadedFile $file, int $idPengajuan, int $idSyarat): BerkasPengajuan
    {
        $this->validasiBerkas($file);

        $mime = $file->getMimeType();
        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            default => 'bin',
        };
        $safeName = Str::random(40).'.'.$extension;
        $folder = "berkas/{$idPengajuan}";

        // Simpan ke disk privat (lokal di development, S3/Supabase di production serverless)
        $path = $file->storeAs($folder, $safeName, 'private');

        $ukuranKb = (int) max(1, ceil($file->getSize() / 1024));

        return BerkasPengajuan::updateOrCreate(
            [
                'id_pengajuan' => $idPengajuan,
                'id_syarat' => $idSyarat,
            ],
            [
                'nama_file_asli' => substr($file->getClientOriginalName(), 0, 150),
                'path_file' => $path,
                'mime' => $mime,
                'ukuran_kb' => $ukuranKb,
                'status_verifikasi' => 'menunggu',
                'catatan' => null,
            ]
        );
    }

    /**
     * Menghapus file fisik dari penyimpanan privat.
     */
    public function hapusFileFisik(string $path): bool
    {
        if (Storage::disk('private')->exists($path)) {
            return Storage::disk('private')->delete($path);
        }

        return false;
    }

    /**
     * Menghapus seluruh folder berkas milik pengajuan tertentu dari disk privat.
     */
    public function hapusDirektoriPengajuan(int $idPengajuan): bool
    {
        $folder = "berkas/{$idPengajuan}";
        if (Storage::disk('private')->directoryExists($folder) || Storage::disk('private')->exists($folder)) {
            return Storage::disk('private')->deleteDirectory($folder);
        }

        return false;
    }

    /**
     * Mengembalikan response pratinjau / unduh berkas privat.
     */
    public function tanggapiBerkas(BerkasPengajuan $berkas, bool $unduh = false): BinaryFileResponse|StreamedResponse
    {
        $disk = Storage::disk('private');

        if (! $disk->exists($berkas->path_file)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        $safeFilename = str_replace(['"', "\r", "\n", '\\', '/'], '_', $berkas->nama_file_asli ?: 'berkas');

        if ($unduh) {
            return $disk->download($berkas->path_file, $safeFilename);
        }

        return $disk->response($berkas->path_file, $safeFilename, [
            'Content-Type' => $berkas->mime,
        ]);
    }
}

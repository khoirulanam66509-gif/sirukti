<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratService
{
    /**
     * Konversi angka bulan ke angka Romawi.
     */
    public function konversiBulanRomawi(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$bulan] ?? 'I';
    }

    /**
     * Menghasilkan nomor surat resmi secara atomik via Stored Procedure sp_ambil_nomor_surat.
     */
    public function buatNomorSurat(Pengajuan $pengajuan, int $tahun, ?\DateTimeInterface $waktu = null): string
    {
        $pengaturanSurat = Pengaturan::ambil('surat', [
            'kode_kampung' => 'RH',
            'jabatan_penandatangan' => 'Kepala Kampung',
        ]);

        $kodeKampung = $pengaturanSurat['kode_kampung'] ?? 'RH';
        $kodeLayanan = $pengajuan->layanan->kode;

        $waktuObj = $waktu ? Carbon::instance($waktu) : now('Asia/Jakarta');
        $bulanRomawi = $this->konversiBulanRomawi((int) $waktuObj->month);

        // Panggil Stored Procedure sp_ambil_nomor_surat (ulangi bila nomor sudah pernah dipakai di tabel surat)
        do {
            DB::statement('CALL sp_ambil_nomor_surat(?, ?, @urut)', [$pengajuan->id_layanan, $tahun]);
            $result = DB::select('SELECT @urut AS urut');
            $nomorUrut = $result[0]->urut ?? 1;

            $nomorUrutFormatted = str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
            $noSurat = "{$nomorUrutFormatted}/{$kodeKampung}/{$kodeLayanan}/{$bulanRomawi}/{$tahun}";
        } while (Surat::where('no_surat', $noSurat)->exists());

        return $noSurat;
    }

    /**
     * Menghasilkan kode unik verifikasi surat (32 karakter acak heksadesimal).
     */
    public function buatKodeVerifikasi(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Menghasilkan Data URI SVG QR Code dari URL verifikasi.
     */
    public function generateQrCode(string $url): string
    {
        $svg = QrCode::size(80)->margin(0)->generate($url);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Merender template surat Blade menjadi objek DomPDF.
     */
    public function renderPdf(Pengajuan $pengajuan, ?Surat $surat = null, bool $isDraft = false, ?Pengguna $penandatangan = null): DomPDF
    {
        $pengajuan->loadMissing(['warga.pengguna', 'warga.dusun', 'layanan']);

        $kop = Pengaturan::ambil('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Merdeka No. 1, Kampung Rukti Harjo',
            'kode_pos' => '34155',
            'telepon' => '',
            'whatsapp' => '',
        ]);

        $suratCfg = Pengaturan::ambil('surat', [
            'kode_kampung' => 'RH',
            'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo',
        ]);

        // Tentukan penandatangan
        if (! $penandatangan) {
            $penandatangan = $surat?->penandatangan ?? Pengguna::where('role', 'kepala')->where('status_aktif', true)->first();
        }

        // TTD Base64
        $ttdBase64 = null;
        if (! $isDraft && $penandatangan && ! empty($penandatangan->file_ttd)) {
            if (Storage::disk('private')->exists($penandatangan->file_ttd)) {
                $ttdContent = Storage::disk('private')->get($penandatangan->file_ttd);
                $mimeType = Storage::disk('private')->mimeType($penandatangan->file_ttd) ?: 'image/png';
                $ttdBase64 = 'data:'.$mimeType.';base64,'.base64_encode($ttdContent);
            }
        }

        // Stempel Base64
        $stempelBase64 = null;
        if (! $isDraft && $penandatangan && ! empty($penandatangan->file_stempel)) {
            if (Storage::disk('private')->exists($penandatangan->file_stempel)) {
                $stempelContent = Storage::disk('private')->get($penandatangan->file_stempel);
                $mimeType = Storage::disk('private')->mimeType($penandatangan->file_stempel) ?: 'image/png';
                $stempelBase64 = 'data:'.$mimeType.';base64,'.base64_encode($stempelContent);
            }
        }

        // QR Base64
        $qrBase64 = null;
        if (! $isDraft && $surat && ! empty($surat->kode_verifikasi)) {
            $verifikasiUrl = url('/verifikasi/'.$surat->kode_verifikasi);
            $qrBase64 = $this->generateQrCode($verifikasiUrl);
        }

        $judulSurat = match ($pengajuan->layanan->kode) {
            'SKCK' => 'SURAT PENGANTAR CATATAN KEPOLISIAN',
            'DOM' => 'SURAT KETERANGAN DOMISILI',
            'SKTM' => 'SURAT KETERANGAN TIDAK MAMPU',
            'SKU' => 'SURAT KETERANGAN USAHA',
            default => 'SURAT KETERANGAN',
        };

        $kodeView = strtolower($pengajuan->layanan->kode);
        $viewName = "surat.templates.{$kodeView}";

        if (! view()->exists($viewName)) {
            $viewName = 'surat.templates.skck';
        }

        $tglSuratFormatted = $surat?->tgl_surat
            ? tgl_indo($surat->tgl_surat)
            : tgl_indo(now());

        $data = [
            'pengajuan' => $pengajuan,
            'warga' => $pengajuan->warga,
            'layanan' => $pengajuan->layanan,
            'data_isian' => $pengajuan->data_isian ?? [],
            'kop' => $kop,
            'surat_cfg' => $suratCfg,
            'penandatangan' => $penandatangan,
            'judul_surat' => $judulSurat,
            'no_surat' => $surat?->no_surat,
            'tgl_surat' => $tglSuratFormatted,
            'ttd_base64' => $ttdBase64,
            'stempel_base64' => $stempelBase64,
            'qr_base64' => $qrBase64,
            'is_draft' => $isDraft,
        ];

        return Pdf::loadView($viewName, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', false);
    }

    /**
     * Menyetujui permohonan surat oleh Kepala Kampung, membentuk nomor surat atomik,
     * menyisipkan tanda tangan & QR, menyimpan file PDF final, dan mengubah status menjadi 'selesai'.
     *
     * @throws ValidationException
     */
    public function setujui(Pengajuan $pengajuan, Pengguna $kepala): Surat
    {
        // 1. Validasi status pengajuan
        if ($pengajuan->status !== 'menunggu_ttd') {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan tidak dalam status menunggu tanda tangan Kepala Kampung.',
            ]);
        }

        // 2. Validasi role kepala
        if ($kepala->role !== 'kepala') {
            throw ValidationException::withMessages([
                'role' => 'Hanya akun Kepala Kampung yang berhak menyetujui permohonan surat.',
            ]);
        }

        // 3. Validasi keberadaan tanda tangan digital
        if (empty($kepala->file_ttd) || ! Storage::disk('private')->exists($kepala->file_ttd)) {
            throw ValidationException::withMessages([
                'file_ttd' => 'Anda belum mengunggah spesimen tanda tangan digital. Silakan unggah tanda tangan Anda pada menu Profil & Tanda Tangan terlebih dahulu.',
            ]);
        }

        $waktuSekarang = now('Asia/Jakarta');
        $tahun = (int) $waktuSekarang->year;

        // Pastikan Stored Procedure sp_ambil_nomor_surat tersedia di database aktif (sebelum membuka transaksi)
        $this->pastikanStoredProcedureTersedia();

        return DB::transaction(function () use ($pengajuan, $kepala, $tahun, $waktuSekarang) {
            // Ambil nomor surat atomik
            $noSurat = $this->buatNomorSurat($pengajuan, $tahun, $waktuSekarang);
            $kodeVerifikasi = $this->buatKodeVerifikasi();
            $tglSurat = $waktuSekarang->toDateString();
            $pathFilePdf = 'surat/'.$tahun.'/'.$pengajuan->no_pengajuan.'.pdf';

            // Catat record Surat
            $surat = Surat::create([
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'no_surat' => $noSurat,
                'tgl_surat' => $tglSurat,
                'kode_verifikasi' => $kodeVerifikasi,
                'file_pdf' => $pathFilePdf,
                'id_penandatangan' => $kepala->id_pengguna,
                'ditandatangani_at' => $waktuSekarang,
            ]);

            // Render PDF final dengan nomor resmi, TTD digital, dan QR code verifikasi
            $pdf = $this->renderPdf($pengajuan, $surat, false, $kepala);

            // Simpan file PDF ke disk privat
            Storage::disk('private')->put($pathFilePdf, $pdf->output());

            // Perbarui status pengajuan menjadi 'selesai' (trigger trg_pengajuan_bu mengisi tgl_selesai)
            $pengajuan->update([
                'status' => 'selesai',
                'id_pengubah' => $kepala->id_pengguna,
                'catatan_petugas' => 'Surat telah disetujui dan ditandatangani oleh Kepala Kampung',
            ]);

            return $surat;
        });
    }

    /**
     * Mengembalikan permohonan surat ke Kasi (status kembali 'diproses') beserta catatan koreksi.
     *
     * @throws ValidationException
     */
    public function kembalikan(Pengajuan $pengajuan, Pengguna $kepala, string $catatan): Pengajuan
    {
        if ($pengajuan->status !== 'menunggu_ttd') {
            throw ValidationException::withMessages([
                'status' => 'Hanya permohonan berstatus menunggu TTD yang dapat dikembalikan.',
            ]);
        }

        $catatanBersih = trim($catatan);
        if (empty($catatanBersih)) {
            throw ValidationException::withMessages([
                'catatan' => 'Catatan alasan pengembalian ke petugas verifikator wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($pengajuan, $kepala, $catatanBersih) {
            $pengajuan->update([
                'status' => 'diproses',
                'id_pengubah' => $kepala->id_pengguna,
                'catatan_petugas' => $catatanBersih,
            ]);

            return $pengajuan->fresh();
        });
    }

    /**
     * Memastikan Stored Procedure sp_ambil_nomor_surat tersedia di database aktif
     * (mencegah error 1305 apabila database diimpor ulang tanpa --routines).
     */
    protected function pastikanStoredProcedureTersedia(): void
    {
        $exists = DB::select(
            "SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE' AND ROUTINE_NAME = 'sp_ambil_nomor_surat'"
        );

        if (empty($exists)) {
            $migration = require database_path('migrations/2026_10_19_000014_create_stored_procedures.php');
            $migration->up();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\BerkasPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $kepala = Pengguna::where('role', 'kepala')->first();
        $wargaList = Warga::with('pengguna')->get();
        $layananList = Layanan::with('syarat')->get();

        if ($wargaList->isEmpty() || $layananList->isEmpty()) {
            return;
        }

        $samplePengajuan = [
            // 1. Menunggu
            [
                'warga_idx' => 0,
                'layanan_kode' => 'SKCK',
                'status' => 'menunggu',
                'data_isian' => ['keperluan' => 'Melamar pekerjaan di BUMN'],
                'tgl' => '2026-10-10 08:30:00',
            ],
            [
                'warga_idx' => 1,
                'layanan_kode' => 'DOM',
                'status' => 'menunggu',
                'data_isian' => [
                    'alamat_domisili' => 'RT 002 / RW 001, Dusun I B, Rukti Harjo',
                    'lama_tinggal_tahun' => 5,
                    'keperluan' => 'Pendaftaran rekening bank',
                ],
                'tgl' => '2026-10-12 09:15:00',
            ],
            [
                'warga_idx' => 2,
                'layanan_kode' => 'SKTM',
                'status' => 'menunggu',
                'data_isian' => [
                    'tujuan' => 'Permohonan beasiswa perkuliahan',
                    'penghasilan_per_bulan' => 1200000,
                    'jumlah_tanggungan' => 3,
                ],
                'tgl' => '2026-10-15 10:00:00',
            ],

            // 2. Kurang Berkas
            [
                'warga_idx' => 3,
                'layanan_kode' => 'SKU',
                'status' => 'kurang_berkas',
                'catatan' => 'Foto tempat usaha buram dan tidak tampak plang usaha',
                'data_isian' => [
                    'nama_usaha' => 'Warung Kelontong Astuti',
                    'jenis_usaha' => 'Perdagangan Sembako',
                    'alamat_usaha' => 'Dusun III RT 001 / RW 003',
                    'lama_usaha_tahun' => 3,
                ],
                'tgl' => '2026-10-08 11:20:00',
            ],
            [
                'warga_idx' => 4,
                'layanan_kode' => 'SKCK',
                'status' => 'kurang_berkas',
                'catatan' => 'Foto Kartu Keluarga terpotong pada bagian tanda tangan camat/kades',
                'data_isian' => ['keperluan' => 'Pendaftaran seleksi PPPK'],
                'tgl' => '2026-10-09 13:45:00',
            ],

            // 3. Sedang Diproses
            [
                'warga_idx' => 5,
                'layanan_kode' => 'DOM',
                'status' => 'diproses',
                'data_isian' => [
                    'alamat_domisili' => 'Dusun V A RT 001 / RW 005',
                    'lama_tinggal_tahun' => 2,
                    'keperluan' => 'Pengurusan mutasi kependudukan',
                ],
                'tgl' => '2026-10-05 08:00:00',
            ],
            [
                'warga_idx' => 6,
                'layanan_kode' => 'SKTM',
                'status' => 'diproses',
                'data_isian' => [
                    'tujuan' => 'Bantuan pengobatan rawat inap RSUD',
                    'penghasilan_per_bulan' => 900000,
                    'jumlah_tanggungan' => 4,
                ],
                'tgl' => '2026-10-06 14:00:00',
            ],
            [
                'warga_idx' => 7,
                'layanan_kode' => 'SKU',
                'status' => 'diproses',
                'data_isian' => [
                    'nama_usaha' => 'Bengkel Motor Lestari',
                    'jenis_usaha' => 'Jasa Servis Motor',
                    'alamat_usaha' => 'Dusun VII RT 002 / RW 007',
                    'lama_usaha_tahun' => 4,
                ],
                'tgl' => '2026-10-07 10:30:00',
            ],

            // 4. Menunggu TTD Kepala
            [
                'warga_idx' => 0,
                'layanan_kode' => 'DOM',
                'status' => 'menunggu_ttd',
                'data_isian' => [
                    'alamat_domisili' => 'RT 001 / RW 001, Dusun I A',
                    'lama_tinggal_tahun' => 10,
                    'keperluan' => 'Pengurusan paspor umroh',
                ],
                'tgl' => '2026-10-03 09:00:00',
            ],
            [
                'warga_idx' => 1,
                'layanan_kode' => 'SKU',
                'status' => 'menunggu_ttd',
                'data_isian' => [
                    'nama_usaha' => 'Kios Berkah Jaya',
                    'jenis_usaha' => 'Perdagangan Pakan Ternak',
                    'alamat_usaha' => 'Dusun I B RT 002',
                    'lama_usaha_tahun' => 2,
                ],
                'tgl' => '2026-10-04 11:00:00',
            ],

            // 5. Selesai (Surat Terbit)
            [
                'warga_idx' => 2,
                'layanan_kode' => 'SKCK',
                'status' => 'selesai',
                'data_isian' => ['keperluan' => 'Melamar pekerjaan swasta di Bandar Lampung'],
                'tgl' => '2026-09-25 08:30:00',
            ],
            [
                'warga_idx' => 3,
                'layanan_kode' => 'DOM',
                'status' => 'selesai',
                'data_isian' => [
                    'alamat_domisili' => 'Dusun III RT 001',
                    'lama_tinggal_tahun' => 7,
                    'keperluan' => 'Persyaratan kredit perumahan (KPR)',
                ],
                'tgl' => '2026-09-28 09:30:00',
            ],
            [
                'warga_idx' => 4,
                'layanan_kode' => 'SKTM',
                'status' => 'selesai',
                'data_isian' => [
                    'tujuan' => 'Keringanan UKT semester mahasiswa',
                    'penghasilan_per_bulan' => 1100000,
                    'jumlah_tanggungan' => 2,
                ],
                'tgl' => '2026-10-01 10:00:00',
            ],

            // 6. Ditolak
            [
                'warga_idx' => 5,
                'layanan_kode' => 'SKTM',
                'status' => 'ditolak',
                'catatan' => 'Berdasarkan verifikasi lapangan, pemohon tidak memenuhi kriteria keluarga prasejahtera.',
                'data_isian' => [
                    'tujuan' => 'Bantuan sosial tunai',
                    'penghasilan_per_bulan' => 4500000,
                    'jumlah_tanggungan' => 1,
                ],
                'tgl' => '2026-09-20 13:00:00',
            ],
            [
                'warga_idx' => 6,
                'layanan_kode' => 'SKU',
                'status' => 'ditolak',
                'catatan' => 'Alamat lokasi usaha berada di luar wilayah Kampung Rukti Harjo.',
                'data_isian' => [
                    'nama_usaha' => 'Toko Busana Triyono',
                    'jenis_usaha' => 'Penjualan Pakaian',
                    'alamat_usaha' => 'Kecamatan Seputih Raman Pasar Pusat',
                    'lama_usaha_tahun' => 1,
                ],
                'tgl' => '2026-09-22 15:30:00',
            ],
        ];

        $counter = 1;
        $tahun = 2026;

        foreach ($samplePengajuan as $idx => $sample) {
            $warga = $wargaList[$sample['warga_idx'] % $wargaList->count()];
            $layanan = $layananList->firstWhere('kode', $sample['layanan_kode']) ?? $layananList->first();

            $tglBuat = Carbon::parse($sample['tgl']);
            $noPengajuan = sprintf('PG-%s-%04d', $tglBuat->format('Ymd'), $counter++);

            // Insert pengajuan (trigger trg_pengajuan_ai akan otomatis mencatat status_log)
            $pengajuan = Pengajuan::create([
                'no_pengajuan' => $noPengajuan,
                'id_warga' => $warga->id_warga,
                'id_layanan' => $layanan->id_layanan,
                'tgl_pengajuan' => $tglBuat,
                'status' => 'menunggu',
                'data_isian' => $sample['data_isian'],
                'id_pengubah' => $warga->id_pengguna,
                'catatan_petugas' => null,
            ]);

            // Tambahkan berkas per syarat
            foreach ($layanan->syarat as $syarat) {
                $statusBerkas = 'menunggu';
                $catatanBerkas = null;

                if ($sample['status'] === 'kurang_berkas' && $syarat->urutan === 2) {
                    $statusBerkas = 'perlu_perbaikan';
                    $catatanBerkas = $sample['catatan'] ?? 'Berkas perlu diperbaiki';
                } elseif (in_array($sample['status'], ['diproses', 'menunggu_ttd', 'selesai'])) {
                    $statusBerkas = 'sesuai';
                }

                BerkasPengajuan::create([
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'id_syarat' => $syarat->id_syarat,
                    'nama_file_asli' => strtolower(str_replace(' ', '_', $syarat->nama_syarat)).'_dummy.jpg',
                    'path_file' => 'berkas_dummy/dummy.jpg',
                    'mime' => 'image/jpeg',
                    'ukuran_kb' => 350,
                    'status_verifikasi' => $statusBerkas,
                    'catatan' => $catatanBerkas,
                ]);
            }

            // Alur perpindahan status bertahap via UPDATE agar trigger trg_pengajuan_au mencatat riwayat
            if ($sample['status'] === 'kurang_berkas') {
                $pengajuan->update([
                    'status' => 'kurang_berkas',
                    'id_verifikator' => $kasi->id_pengguna,
                    'id_pengubah' => $kasi->id_pengguna,
                    'catatan_petugas' => $sample['catatan'],
                ]);
            } elseif ($sample['status'] === 'diproses') {
                $pengajuan->update([
                    'status' => 'diproses',
                    'id_verifikator' => $kasi->id_pengguna,
                    'id_pengubah' => $kasi->id_pengguna,
                    'tgl_diproses' => $tglBuat->copy()->addHours(2),
                ]);
            } elseif ($sample['status'] === 'menunggu_ttd') {
                $pengajuan->update([
                    'status' => 'diproses',
                    'id_verifikator' => $kasi->id_pengguna,
                    'id_pengubah' => $kasi->id_pengguna,
                    'tgl_diproses' => $tglBuat->copy()->addHours(2),
                ]);
                $pengajuan->update([
                    'status' => 'menunggu_ttd',
                    'id_pengubah' => $kasi->id_pengguna,
                    'catatan_petugas' => 'Berkas lengkap dan terverifikasi, diajukan untuk tanda tangan Kepala Kampung',
                ]);
            } elseif ($sample['status'] === 'selesai') {
                $pengajuan->update([
                    'status' => 'diproses',
                    'id_verifikator' => $kasi->id_pengguna,
                    'id_pengubah' => $kasi->id_pengguna,
                    'tgl_diproses' => $tglBuat->copy()->addHours(2),
                ]);
                $pengajuan->update([
                    'status' => 'menunggu_ttd',
                    'id_pengubah' => $kasi->id_pengguna,
                ]);

                // Panggil stored procedure sp_ambil_nomor_surat
                DB::statement('CALL sp_ambil_nomor_surat(?, ?, @p_urut)', [$layanan->id_layanan, $tahun]);
                $urut = DB::select('SELECT @p_urut AS urut')[0]->urut ?? 1;

                $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$tglBuat->month - 1];
                $noSurat = sprintf('%03d/%s/RH/%s/%d', $urut, $layanan->kode, $bulanRomawi, $tahun);
                $kodeVerifikasi = Str::random(32);

                $tglSelesai = $tglBuat->copy()->addHours(5);

                $pengajuan->update([
                    'status' => 'selesai',
                    'id_pengubah' => $kepala->id_pengguna,
                    'tgl_selesai' => $tglSelesai,
                    'catatan_petugas' => 'Surat telah disetujui dan ditandatangani',
                ]);

                Surat::create([
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'no_surat' => $noSurat,
                    'tgl_surat' => $tglSelesai->toDateString(),
                    'kode_verifikasi' => $kodeVerifikasi,
                    'file_pdf' => 'surat_selesai/dummy_'.$pengajuan->id_pengajuan.'.pdf',
                    'id_penandatangan' => $kepala->id_pengguna,
                    'ditandatangani_at' => $tglSelesai,
                ]);
            } elseif ($sample['status'] === 'ditolak') {
                $pengajuan->update([
                    'status' => 'ditolak',
                    'id_verifikator' => $kasi->id_pengguna,
                    'id_pengubah' => $kasi->id_pengguna,
                    'catatan_petugas' => $sample['catatan'],
                ]);
            }
        }
    }
}

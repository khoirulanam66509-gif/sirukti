<?php

namespace Tests\Feature\Kasi;

use App\Models\BerkasPengajuan;
use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\SyaratLayanan;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerifikasiMintaPerbaikanTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Layanan $layanan;

    protected SyaratLayanan $syaratKtp;

    protected SyaratLayanan $syaratKk;

    protected function setUp(): void
    {
        parent::setUp();

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I Test']);

        $this->kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan Test',
            'username' => 'kasi_r6_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Warga Pemohon R6',
            'username' => '1802011111'.rand(100000, 999999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => $this->wargaUser->username,
            'no_kk' => '1802011111000001',
            'id_dusun' => $dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Uji No. 6',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1992-02-02',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Wiraswasta',
        ]);

        $this->layanan = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        $this->syaratKtp = SyaratLayanan::firstOrCreate(
            ['id_layanan' => $this->layanan->id_layanan, 'nama_syarat' => 'Foto KTP'],
            ['petunjuk' => 'Foto KTP asli', 'wajib' => true, 'urutan' => 1, 'aktif' => true]
        );

        $this->syaratKk = SyaratLayanan::firstOrCreate(
            ['id_layanan' => $this->layanan->id_layanan, 'nama_syarat' => 'Foto Kartu Keluarga'],
            ['petunjuk' => 'Foto KK asli', 'wajib' => true, 'urutan' => 2, 'aktif' => true]
        );
    }

    /**
     * Uji: Klik tombol 'Minta Perbaikan' tanpa menandai satupun berkas sebagai perlu_perbaikan
     * harus memunculkan pesan validasi Bahasa Indonesia yang ramah, bukan 'validation.required_with'.
     */
    public function test_klik_minta_perbaikan_tanpa_berkas_ditandai_menghasilkan_pesan_ramah_bahasa_indonesia(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9901',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas1 = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKtp->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 200,
            'status_verifikasi' => 'menunggu',
        ]);

        $berkas2 = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKk->id_syarat,
            'nama_file_asli' => 'kk.jpg',
            'path_file' => 'berkas/kk.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'menunggu',
        ]);

        // Simulasikan submit dari form ketika seluruh berkas masih berstatus 'menunggu'
        $response = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'minta_perbaikan',
                'berkas' => [
                    $berkas1->id_berkas => [
                        'status_verifikasi' => 'menunggu',
                        'catatan' => null,
                    ],
                    $berkas2->id_berkas => [
                        'status_verifikasi' => 'menunggu',
                        'catatan' => null,
                    ],
                ],
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHasErrors(['berkas']);

        // Pastikan TIDAK ADA teks mentah 'validation.required_with'
        $errors = session('errors')->all();
        $this->assertNotEmpty($errors);
        foreach ($errors as $error) {
            $this->assertStringNotContainsString('validation.required_with', $error);
            $this->assertStringNotContainsString('validation.', $error);
        }

        // Pastikan pesan yang muncul ramah
        $this->assertContains('Tandai minimal satu berkas yang perlu diperbaiki beserta alasannya.', $errors);
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);
    }

    /**
     * Uji: Klik tombol 'Minta Perbaikan' dengan berkas ditandai perlu_perbaikan tetapi catatan kosong
     * harus memvalidasi kewajiban catatan dengan nama berkas yang spesifik.
     */
    public function test_klik_minta_perbaikan_dengan_berkas_tanpa_catatan_meminta_alasan_perbaikan(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9902',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas1 = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKtp->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 200,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'minta_perbaikan',
                'berkas' => [
                    $berkas1->id_berkas => [
                        'status_verifikasi' => 'perlu_perbaikan',
                        'catatan' => '   ', // spasi kosong
                    ],
                ],
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHasErrors(["berkas.{$berkas1->id_berkas}.catatan"]);

        $errorMessage = session('errors')->first("berkas.{$berkas1->id_berkas}.catatan");
        $this->assertStringContainsString('Alasan perbaikan untuk berkas Foto KTP wajib diisi', $errorMessage);
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);
    }

    /**
     * Uji: Klik tombol 'Minta Perbaikan' dengan berkas ditandai dan catatan terisi
     * berhasil mengubah status pengajuan ke 'kurang_berkas' dan menyimpan catatan per berkas.
     */
    public function test_klik_minta_perbaikan_sukses_mengubah_status_dan_mencatat_log(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9903',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas1 = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKtp->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 200,
            'status_verifikasi' => 'menunggu',
        ]);

        $berkas2 = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKk->id_syarat,
            'nama_file_asli' => 'kk.jpg',
            'path_file' => 'berkas/kk.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)
            ->from(route('kasi.pengajuan.show', $pengajuan))
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'minta_perbaikan',
                'berkas' => [
                    $berkas1->id_berkas => [
                        'status_verifikasi' => 'perlu_perbaikan',
                        'catatan' => 'Foto KTP buram, harap foto ulang',
                    ],
                    $berkas2->id_berkas => [
                        'status_verifikasi' => 'sesuai',
                        'catatan' => null,
                    ],
                ],
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHas('status');

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('kurang_berkas', $pengajuanSegar->status);

        $berkas1Segar = $berkas1->fresh();
        $this->assertEquals('perlu_perbaikan', $berkas1Segar->status_verifikasi);
        $this->assertEquals('Foto KTP buram, harap foto ulang', $berkas1Segar->catatan);

        $berkas2Segar = $berkas2->fresh();
        $this->assertEquals('sesuai', $berkas2Segar->status_verifikasi);
        $this->assertNull($berkas2Segar->catatan);

        // Status log tercatat
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'menunggu',
            'status_baru' => 'kurang_berkas',
        ]);
    }

    /**
     * Uji: Halaman show verifikasi merender markup tombol dan error inline dengan benar.
     */
    public function test_tampilan_verifikasi_merender_kontrol_dan_error_inline(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9904',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratKtp->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 200,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('Minta Perbaikan');
        $response->assertSee('✓ Sesuai &amp; Jelas', false);
        $response->assertSee('⚠️ Perlu Perbaikan');
        $response->assertSee('name="berkas['.$berkas->id_berkas.'][status_verifikasi]"', false);
        $response->assertSee('name="berkas['.$berkas->id_berkas.'][catatan]"', false);
        $response->assertSee("statusVerifikasi: 'menunggu'", false);
    }

    /**
     * Uji: Role selain Kasi (misal warga) tidak dapat melakukan aksi verifikasi (403).
     */
    public function test_akses_verifikasi_oleh_selain_kasi_ditolak_403(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-9905',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $response = $this->actingAs($this->wargaUser)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'minta_perbaikan',
            ]);

        $response->assertStatus(403);
    }
}

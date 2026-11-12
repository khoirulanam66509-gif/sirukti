<?php

namespace Tests\Feature;

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

class KasiVerifikasiPengajuanTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $wargaUser;

    protected Pengguna $kepala;

    protected Warga $warga;

    protected Layanan $layananSkck;

    protected Layanan $layananSku;

    protected SyaratLayanan $syaratSkck;

    protected SyaratLayanan $syaratSku;

    protected function setUp(): void
    {
        parent::setUp();

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        // Akun Kasi
        $this->kasi = Pengguna::create([
            'nama' => 'Petugas Kasi',
            'username' => 'kasi_test_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        // Akun Kepala Desa
        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Test',
            'username' => 'kepala_test_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        // Akun Warga
        $this->wargaUser = Pengguna::create([
            'nama' => 'Warga Pemohon Test',
            'username' => '180201010190'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => $this->wargaUser->username,
            'no_kk' => '1802010101900001',
            'id_dusun' => $dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Test No. 1',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Wiraswasta',
        ]);

        $this->layananSkck = Layanan::firstOrCreate(
            ['kode' => 'SKCK'],
            ['nama_layanan' => 'Surat Pengantar SKCK', 'deskripsi' => 'Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
        );

        $this->layananSku = Layanan::firstOrCreate(
            ['kode' => 'SKU'],
            ['nama_layanan' => 'Surat Keterangan Usaha', 'deskripsi' => 'Keterangan Usaha', 'status_aktif' => true, 'urutan' => 4]
        );

        $this->syaratSkck = SyaratLayanan::firstOrCreate(
            ['id_layanan' => $this->layananSkck->id_layanan, 'nama_syarat' => 'KTP Pemohon'],
            ['petunjuk' => 'Foto KTP', 'wajib' => true, 'urutan' => 1, 'aktif' => true]
        );

        $this->syaratSku = SyaratLayanan::firstOrCreate(
            ['id_layanan' => $this->layananSku->id_layanan, 'nama_syarat' => 'Foto Usaha'],
            ['petunjuk' => 'Foto Tempat Usaha', 'wajib' => true, 'urutan' => 1, 'aktif' => true]
        );
    }

    public function test_halaman_antrean_hanya_dapat_diakses_oleh_kasi(): void
    {
        // 1. Guest redirect ke login
        $responseGuest = $this->get(route('kasi.pengajuan.index'));
        $responseGuest->assertRedirect(route('login'));

        // 2. Warga mendapat 403 Forbidden
        $responseWarga = $this->actingAs($this->wargaUser)->get(route('kasi.pengajuan.index'));
        $responseWarga->assertStatus(403);

        // 3. Kepala Desa mendapat 403 Forbidden
        $responseKepala = $this->actingAs($this->kepala)->get(route('kasi.pengajuan.index'));
        $responseKepala->assertStatus(403);

        // 4. Kasi dapat mengakses antrean dengan sukses
        $responseKasi = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.index'));
        $responseKasi->assertStatus(200);
        $responseKasi->assertSee('Antrean Verifikasi Surat');
    }

    public function test_filter_dan_pencarian_antrean_berfungsi(): void
    {
        $pengajuan1 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8801',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now()->subHours(2),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Melamar Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $pengajuan2 = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8802',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSku->id_layanan,
            'tgl_pengajuan' => now()->subHour(),
            'status' => 'diproses',
            'data_isian' => ['nama_usaha' => 'Toko Kelontong Berkah'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        // Filter status menunggu
        $responseMenunggu = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.index', ['status' => 'menunggu']));
        $responseMenunggu->assertStatus(200);
        $responseMenunggu->assertSee($pengajuan1->no_pengajuan);
        $responseMenunggu->assertDontSee($pengajuan2->no_pengajuan);

        // Filter jenis layanan SKU
        $responseLayanan = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.index', ['layanan' => 'SKU']));
        $responseLayanan->assertStatus(200);
        $responseLayanan->assertSee($pengajuan2->no_pengajuan);
        $responseLayanan->assertDontSee($pengajuan1->no_pengajuan);

        // Pencarian dengan nomor permohonan
        $responseCari = $this->actingAs($this->kasi)
            ->get(route('kasi.pengajuan.index', ['cari' => '8801']));
        $responseCari->assertStatus(200);
        $responseCari->assertSee($pengajuan1->no_pengajuan);
        $responseCari->assertDontSee($pengajuan2->no_pengajuan);
    }

    public function test_kasi_dapat_melihat_detail_dan_berkas_pengajuan(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8803',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pendaftaran CPNS'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp_asli.jpg',
            'path_file' => 'berkas/test_ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 200,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.show', $pengajuan));
        $response->assertStatus(200);
        $response->assertSee($pengajuan->no_pengajuan);
        $response->assertSee($this->wargaUser->nama);
        $response->assertSee('Pendaftaran CPNS');
        $response->assertSee('ktp_asli.jpg');
        $response->assertSee('showModalTolak');
        $response->assertSee('relative z-10 min-h-full flex items-end sm:items-center justify-center', false);
        $response->assertSee('Tolak Permohonan Surat');
    }

    public function test_kasi_dapat_memverifikasi_dan_memproses_pengajuan_bila_semua_berkas_sesuai(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8804',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Melamar Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'proses',
                'berkas' => [
                    $berkas->id_berkas => [
                        'status_verifikasi' => 'sesuai',
                        'catatan' => null,
                    ],
                ],
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $response->assertSessionHas('status');

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('diproses', $pengajuanSegar->status);
        $this->assertEquals($this->kasi->id_pengguna, $pengajuanSegar->id_verifikator);
        $this->assertNotNull($pengajuanSegar->tgl_diproses);

        // Periksa status_log tercatat
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'menunggu',
            'status_baru' => 'diproses',
            'id_pengguna' => $this->kasi->id_pengguna,
        ]);
    }

    public function test_kasi_gagal_memproses_bila_ada_berkas_yang_belum_sesuai(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8805',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'menunggu',
        ]);

        // Coba proses tanpa mengubah status berkas menjadi sesuai
        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'proses',
                'berkas' => [
                    $berkas->id_berkas => [
                        'status_verifikasi' => 'menunggu',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('berkas');
        $this->assertEquals('menunggu', $pengajuan->fresh()->status);
    }

    public function test_kasi_dapat_meminta_perbaikan_berkas_dan_mengubah_status_ke_kurang_berkas(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8806',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp_buram.jpg',
            'path_file' => 'berkas/ktp_buram.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'minta_perbaikan',
                'catatan_petugas' => 'Harap upload ulang foto KTP yang jelas',
                'berkas' => [
                    $berkas->id_berkas => [
                        'status_verifikasi' => 'perlu_perbaikan',
                        'catatan' => 'Foto KTP buram dan NIK tidak terbaca',
                    ],
                ],
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('kurang_berkas', $pengajuanSegar->status);
        $this->assertEquals('perlu_perbaikan', $berkas->fresh()->status_verifikasi);
        $this->assertEquals('Foto KTP buram dan NIK tidak terbaca', $berkas->fresh()->catatan);

        // Cek log status
        $this->assertDatabaseHas('status_log', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_lama' => 'menunggu',
            'status_baru' => 'kurang_berkas',
        ]);
    }

    public function test_kasi_dapat_mengajukan_pengajuan_diproses_ke_kepala_desa(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8807',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->kasi->id_pengguna,
        ]);

        $berkas = BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp_jelas.jpg',
            'path_file' => 'berkas/ktp_jelas.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 250,
            'status_verifikasi' => 'sesuai',
        ]);

        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'ajukan_ttd',
            ]);

        $response->assertRedirect(route('kasi.pengajuan.show', $pengajuan));
        $this->assertEquals('menunggu_ttd', $pengajuan->fresh()->status);
    }

    public function test_kasi_dapat_menolak_pengajuan_dengan_alasan_wajib(): void
    {
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8808',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        // 1. Gagal jika alasan kosong
        $responseKosong = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => '   ',
            ]);
        $responseKosong->assertSessionHasErrors('catatan_petugas');

        // 2. Berhasil jika alasan diisi
        $responseValid = $this->actingAs($this->kasi)
            ->post(route('kasi.pengajuan.verifikasi', $pengajuan), [
                'aksi' => 'tolak',
                'catatan_petugas' => 'Pemohon terbukti bukan warga tetap Kampung Rukti Harjo.',
            ]);

        $responseValid->assertRedirect(route('kasi.pengajuan.show', $pengajuan));

        $pengajuanSegar = $pengajuan->fresh();
        $this->assertEquals('ditolak', $pengajuanSegar->status);
        $this->assertEquals('Pemohon terbukti bukan warga tetap Kampung Rukti Harjo.', $pengajuanSegar->catatan_petugas);
    }

    public function test_halaman_show_kasi_menampilkan_tombol_whatsapp_dan_preset_alasan_perbaikan(): void
    {
        $this->wargaUser->update(['no_hp' => '081234567890']);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-8809',
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pekerjaan'],
            'id_pengubah' => $this->wargaUser->id_pengguna,
        ]);

        BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $this->syaratSkck->id_syarat,
            'nama_file_asli' => 'ktp_pemohon.jpg',
            'path_file' => 'berkas/ktp_pemohon.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 300,
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kasi)->get(route('kasi.pengajuan.show', $pengajuan));
        $response->assertStatus(200);

        // Memuat tombol WhatsApp pemohon
        $response->assertSee('WhatsApp Pemohon');
        $response->assertSee('https://wa.me/6281234567890', false);

        // Memuat preset saran cepat alasan perbaikan berkas
        $response->assertSee('Foto Buram');
        $response->assertSee('Dokumen Terpotong');
    }
}

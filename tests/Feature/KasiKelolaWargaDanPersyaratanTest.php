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

class KasiKelolaWargaDanPersyaratanTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $kepala;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Dusun $dusun;

    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        $this->kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan Rukti',
            'username' => 'kasi_kw_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Rukti',
            'username' => 'kepala_kw_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Budi Santoso',
            'username' => '180201101092'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => '1802011010920001',
            'no_kk' => '1802011010920002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1992-10-10',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Poros Desa No. 5',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '002',
        ]);

        $this->layanan = Layanan::where('kode', 'SKCK')->first()
            ?? Layanan::firstOrCreate(
                ['kode' => 'SKCK'],
                ['nama_layanan' => 'Surat Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
            );
    }

    public function test_halaman_kelola_warga_hanya_dapat_diakses_kasi(): void
    {
        // Tamu -> login
        $this->get(route('kasi.warga.index'))->assertRedirect(route('login'));

        // Warga -> 403
        $this->actingAs($this->wargaUser)->get(route('kasi.warga.index'))->assertStatus(403);

        // Kepala -> 403
        $this->actingAs($this->kepala)->get(route('kasi.warga.index'))->assertStatus(403);

        // Kasi -> 200
        $this->actingAs($this->kasi)->get(route('kasi.warga.index'))->assertStatus(200);
    }

    public function test_kasi_dapat_mencari_dan_memfilter_data_warga(): void
    {
        $response = $this->actingAs($this->kasi)->get(route('kasi.warga.index', ['q' => 'Budi Santoso']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('180201******0001');

        $response2 = $this->actingAs($this->kasi)->get(route('kasi.warga.index', ['q' => 'TidakAdaOrangIni999']));
        $response2->assertDontSee('Budi Santoso');
    }

    public function test_kasi_dapat_mengoreksi_data_kependudukan_warga(): void
    {
        $payload = [
            'nama' => 'Budi Santoso S.Pd',
            'no_hp' => '081299998888',
            'no_kk' => '1802011010920002',
            'tempat_lahir' => 'Metro',
            'tgl_lahir' => '1992-10-10',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Guru',
            'alamat' => 'Jl. Pendidikan Baru No. 10',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '004',
            'rw' => '002',
        ];

        $response = $this->actingAs($this->kasi)->put(route('kasi.warga.update', $this->warga), $payload);

        $response->assertRedirect(route('kasi.warga.index'));
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nama' => 'Budi Santoso S.Pd',
            'no_hp' => '081299998888',
        ]);
        $this->assertDatabaseHas('warga', [
            'id_warga' => $this->warga->id_warga,
            'tempat_lahir' => 'Metro',
            'pekerjaan' => 'Guru',
            'rt' => '004',
        ]);
    }

    public function test_kasi_dapat_mereset_password_sementara_dan_memaksa_warga_ganti_password(): void
    {
        $response = $this->actingAs($this->kasi)->post(route('kasi.warga.reset-password', $this->warga), [
            'password_baru' => 'sementara123',
        ]);

        $response->assertRedirect(route('kasi.warga.index'));
        $response->assertSessionHas('success');

        // Pastikan akun warga memiliki flag wajib_ganti_password = true
        $penggunaFresh = $this->wargaUser->fresh();
        $this->assertTrue((bool) $penggunaFresh->wajib_ganti_password);
        $this->assertTrue(Hash::check('sementara123', $penggunaFresh->password));

        // Verifikasi bahwa warga yang login dengan flag wajib_ganti_password diarahkan ke form ubah password
        $this->actingAs($penggunaFresh)->get(route('dashboard'))->assertRedirect(route('password.change'));
    }

    public function test_kasi_dapat_menonaktifkan_akun_warga_dan_akun_nonaktif_tidak_dapat_login(): void
    {
        // Toggle ke nonaktif
        $response = $this->actingAs($this->kasi)->post(route('kasi.warga.toggle-status', $this->warga));
        $response->assertRedirect(route('kasi.warga.index'));

        $this->assertFalse((bool) $this->wargaUser->fresh()->status_aktif);

        // Akun nonaktif mencoba login
        auth()->logout();
        $loginResponse = $this->post(route('login'), [
            'username' => $this->wargaUser->username,
            'password' => 'password123',
        ]);

        $loginResponse->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_halaman_persyaratan_layanan_hanya_dapat_diakses_kasi(): void
    {
        $this->get(route('kasi.layanan.index'))->assertRedirect(route('login'));
        $this->actingAs($this->wargaUser)->get(route('kasi.layanan.index'))->assertStatus(403);
        $this->actingAs($this->kepala)->get(route('kasi.layanan.index'))->assertStatus(403);
        $this->actingAs($this->kasi)->get(route('kasi.layanan.index'))->assertStatus(200);
    }

    public function test_kasi_dapat_menambah_dan_mengubah_persyaratan_layanan(): void
    {
        // Tambah syarat baru
        $storeResponse = $this->actingAs($this->kasi)->post(route('kasi.layanan.syarat.store', $this->layanan), [
            'nama_syarat' => 'Surat Pengantar RT/RW Tambahan',
            'petunjuk' => 'Foto surat pengantar asli berstempel',
            'urutan' => 9,
            'wajib' => 1,
            'status_aktif' => 1,
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('syarat_layanan', [
            'id_layanan' => $this->layanan->id_layanan,
            'nama_syarat' => 'Surat Pengantar RT/RW Tambahan',
            'urutan' => 9,
            'wajib' => 1,
            'status_aktif' => 1,
        ]);

        $syarat = SyaratLayanan::where('nama_syarat', 'Surat Pengantar RT/RW Tambahan')->first();

        // Update syarat
        $updateResponse = $this->actingAs($this->kasi)->put(route('kasi.layanan.syarat.update', [$this->layanan, $syarat]), [
            'nama_syarat' => 'Surat Pengantar RT/RW Revisi',
            'petunjuk' => 'Petunjuk diperbarui',
            'urutan' => 10,
            'wajib' => 0,
            'status_aktif' => 1,
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('syarat_layanan', [
            'id_syarat' => $syarat->id_syarat,
            'nama_syarat' => 'Surat Pengantar RT/RW Revisi',
            'wajib' => 0,
            'urutan' => 10,
        ]);
    }

    public function test_kasi_dapat_menghapus_syarat_yang_belum_digunakan(): void
    {
        $syaratBaru = SyaratLayanan::create([
            'id_layanan' => $this->layanan->id_layanan,
            'nama_syarat' => 'Syarat Belum Terpakai '.uniqid(),
            'wajib' => false,
            'urutan' => 99,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->kasi)->delete(route('kasi.layanan.syarat.destroy', [$this->layanan, $syaratBaru]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('syarat_layanan', [
            'id_syarat' => $syaratBaru->id_syarat,
        ]);
    }

    public function test_syarat_yang_sudah_digunakan_dalam_berkas_pengajuan_tidak_dapat_dihapus(): void
    {
        $syaratTerpakai = SyaratLayanan::create([
            'id_layanan' => $this->layanan->id_layanan,
            'nama_syarat' => 'Syarat Terpakai '.uniqid(),
            'wajib' => true,
            'urutan' => 88,
            'status_aktif' => true,
        ]);

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Pendaftaran Pekerjaan'],
        ]);

        BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $syaratTerpakai->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'berkas/test.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 100,
            'status_verifikasi' => 'menunggu',
        ]);

        // Coba hapus
        $response = $this->actingAs($this->kasi)->delete(route('kasi.layanan.syarat.destroy', [$this->layanan, $syaratTerpakai]));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Pastikan baris syarat masih ada di database
        $this->assertDatabaseHas('syarat_layanan', [
            'id_syarat' => $syaratTerpakai->id_syarat,
        ]);
    }
}

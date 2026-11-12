<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanDanTtdTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $kepala;

    protected Pengguna $warga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan Test',
            'username' => 'kasi_cfg_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->kepala = Pengguna::create([
            'nama' => 'Kepala Desa Test',
            'username' => 'kepala_cfg_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'jabatan' => 'Kepala Kampung Rukti Harjo',
            'status_aktif' => true,
        ]);

        $this->warga = Pengguna::create([
            'nama' => 'Warga Test',
            'username' => 'warga_cfg_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);
    }

    public function test_halaman_pengaturan_kampung_hanya_dapat_diakses_kasi(): void
    {
        // 1. Guest redirect ke login
        $this->get(route('kasi.pengaturan.edit'))->assertRedirect(route('login'));

        // 2. Warga mendapat 403 Forbidden
        $this->actingAs($this->warga)->get(route('kasi.pengaturan.edit'))->assertStatus(403);

        // 3. Kepala mendapat 403 Forbidden
        $this->actingAs($this->kepala)->get(route('kasi.pengaturan.edit'))->assertStatus(403);

        // 4. Kasi dapat mengakses dengan sukses 200
        $responseKasi = $this->actingAs($this->kasi)->get(route('kasi.pengaturan.edit'));
        $responseKasi->assertStatus(200);
        $responseKasi->assertSee('Pengaturan Kampung');
        $responseKasi->assertSee('Kop', false);
    }

    public function test_kasi_dapat_memperbarui_pengaturan_kampung(): void
    {
        $payload = [
            'nama_kampung' => 'Rukti Harjo Baru',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Protokol No. 10',
            'kode_pos' => '34156',
            'telepon' => '081299887766',
            'whatsapp' => '6281299887766',
            'kode_kampung' => 'RHB',
            'jabatan_penandatangan' => 'Kepala Kampung Rukti Harjo',
            'jam_senin_kamis' => '08.00 - 15.30 WIB',
            'jam_jumat' => '08.00 - 11.00 WIB',
            'catatan_layanan' => 'Layanan tatap muka buka setiap hari kerja.',
            'jumlah_jiwa' => 6200,
            'jumlah_kk' => 1850,
            'sumber_statistik' => 'Sensus 2026',
        ];

        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengaturan.update'), $payload);

        $response->assertRedirect(route('kasi.pengaturan.edit'));
        $response->assertSessionHas('status');

        $profil = Pengaturan::ambil('profil_kampung');
        $this->assertEquals('Rukti Harjo Baru', $profil['nama_kampung']);
        $this->assertEquals('34156', $profil['kode_pos']);

        $surat = Pengaturan::ambil('surat');
        $this->assertEquals('RHB', $surat['kode_kampung']);

        $jam = Pengaturan::ambil('jam_layanan');
        $this->assertEquals('08.00 - 15.30 WIB', $jam['senin_kamis']);

        $statistik = Pengaturan::ambil('statistik_kampung');
        $this->assertEquals(6200, $statistik['jiwa']);
    }

    public function test_validasi_pengaturan_kampung_menolak_data_kosong(): void
    {
        $response = $this->actingAs($this->kasi)
            ->post(route('kasi.pengaturan.update'), [
                'nama_kampung' => '',
                'kode_kampung' => '',
            ]);

        $response->assertSessionHasErrors(['nama_kampung', 'kode_kampung', 'alamat', 'kode_pos']);
    }

    public function test_halaman_profil_ttd_hanya_dapat_diakses_kepala(): void
    {
        // 1. Guest redirect ke login
        $this->get(route('kepala.profil.edit'))->assertRedirect(route('login'));

        // 2. Warga mendapat 403 Forbidden
        $this->actingAs($this->warga)->get(route('kepala.profil.edit'))->assertStatus(403);

        // 3. Kasi mendapat 403 Forbidden
        $this->actingAs($this->kasi)->get(route('kepala.profil.edit'))->assertStatus(403);

        // 4. Kepala dapat mengakses 200 OK
        $responseKepala = $this->actingAs($this->kepala)->get(route('kepala.profil.edit'));
        $responseKepala->assertStatus(200);
        $responseKepala->assertSee('Tanda Tangan Digital');
    }

    public function test_kepala_dapat_memperbarui_data_profil_aparatur(): void
    {
        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.profil.update'), [
                'nama' => 'Bpk. H. Sudirman, S.IP',
                'nip' => '197501012005011005',
                'jabatan' => 'Kepala Kampung Rukti Harjo',
                'no_hp' => '081233445566',
            ]);

        $response->assertRedirect(route('kepala.profil.edit'));
        $response->assertSessionHas('status_profil');

        $kepalaSegar = $this->kepala->fresh();
        $this->assertEquals('Bpk. H. Sudirman, S.IP', $kepalaSegar->nama);
        $this->assertEquals('197501012005011005', $kepalaSegar->nip);
    }

    public function test_kepala_dapat_mengunggah_tanda_tangan_dan_tersimpan_di_disk_privat(): void
    {
        Storage::fake('private');

        $fileTtd = UploadedFile::fake()->image('ttd_kepala.png', 300, 150);

        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.ttd.update'), [
                'file_ttd' => $fileTtd,
            ]);

        $response->assertRedirect(route('kepala.profil.edit'));
        $response->assertSessionHas('status_ttd');

        $kepalaSegar = $this->kepala->fresh();
        $this->assertNotNull($kepalaSegar->file_ttd);

        // Pastikan file tersimpan di storage privat
        Storage::disk('private')->assertExists($kepalaSegar->file_ttd);

        // Pratinjau tanda tangan berhasil diakses oleh Kepala
        $responsePreview = $this->actingAs($this->kepala)->get(route('kepala.ttd.preview'));
        $responsePreview->assertStatus(200);

        // Warga tidak dapat mengakses pratinjau tanda tangan
        $this->actingAs($this->warga)->get(route('kepala.ttd.preview'))->assertStatus(403);
    }

    public function test_kepala_dapat_menghapus_tanda_tangan(): void
    {
        Storage::fake('private');

        $fileTtd = UploadedFile::fake()->image('ttd_lama.png', 300, 150);
        $path = $fileTtd->store('ttd', 'private');
        $this->kepala->update(['file_ttd' => $path]);

        Storage::disk('private')->assertExists($path);

        $response = $this->actingAs($this->kepala)->delete(route('kepala.ttd.destroy'));
        $response->assertRedirect(route('kepala.profil.edit'));
        $response->assertSessionHas('status_ttd');

        $this->assertNull($this->kepala->fresh()->file_ttd);
        Storage::disk('private')->assertMissing($path);
    }

    public function test_kepala_dapat_mengunggah_stempel_dan_tersimpan_di_disk_privat(): void
    {
        Storage::fake('private');

        $fileStempel = UploadedFile::fake()->image('stempel_desa.png', 300, 300);

        $response = $this->actingAs($this->kepala)
            ->post(route('kepala.stempel.update'), [
                'file_stempel' => $fileStempel,
            ]);

        $response->assertRedirect(route('kepala.profil.edit'));
        $response->assertSessionHas('status_stempel');

        $kepalaSegar = $this->kepala->fresh();
        $this->assertNotNull($kepalaSegar->file_stempel);

        // Pastikan file tersimpan di storage privat
        Storage::disk('private')->assertExists($kepalaSegar->file_stempel);

        // Pratinjau stempel berhasil diakses oleh Kepala
        $responsePreview = $this->actingAs($this->kepala)->get(route('kepala.stempel.preview'));
        $responsePreview->assertStatus(200);
    }

    public function test_kepala_dapat_menghapus_stempel(): void
    {
        Storage::fake('private');

        $fileStempel = UploadedFile::fake()->image('stempel_lama.png', 300, 300);
        $path = $fileStempel->store('stempel', 'private');
        $this->kepala->update(['file_stempel' => $path]);

        Storage::disk('private')->assertExists($path);

        $response = $this->actingAs($this->kepala)->delete(route('kepala.stempel.destroy'));
        $response->assertRedirect(route('kepala.profil.edit'));
        $response->assertSessionHas('status_stempel');

        $this->assertNull($this->kepala->fresh()->file_stempel);
        Storage::disk('private')->assertMissing($path);
    }

    public function test_warga_dan_kasi_tidak_dapat_mengakses_pratinjau_stempel(): void
    {
        Storage::fake('private');

        $fileStempel = UploadedFile::fake()->image('stempel_desa.png', 300, 300);
        $path = $fileStempel->store('stempel', 'private');
        $this->kepala->update(['file_stempel' => $path]);

        // Warga mendapat 403 Forbidden
        $this->actingAs($this->warga)->get(route('kepala.stempel.preview'))->assertStatus(403);

        // Kasi mendapat 403 Forbidden
        $this->actingAs($this->kasi)->get(route('kepala.stempel.preview'))->assertStatus(403);
    }
}

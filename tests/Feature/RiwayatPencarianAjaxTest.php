<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPencarianAjaxTest extends TestCase
{
    use RefreshDatabase;

    private Dusun $dusun;

    private Layanan $layananSkck;

    private Layanan $layananSktm;

    private Pengguna $userWarga1;

    private Warga $warga1;

    private Pengguna $userWarga2;

    private Warga $warga2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::create([
            'nama_dusun' => 'Dusun I A',
        ]);

        $this->layananSkck = Layanan::create([
            'kode' => 'SKCK',
            'nama_layanan' => 'Pengantar SKCK',
            'deskripsi' => 'Surat Pengantar SKCK',
            'status_aktif' => true,
            'urutan' => 1,
        ]);

        $this->layananSktm = Layanan::create([
            'kode' => 'SKTM',
            'nama_layanan' => 'Keterangan Tidak Mampu',
            'deskripsi' => 'Surat Keterangan Tidak Mampu',
            'status_aktif' => true,
            'urutan' => 2,
        ]);

        // Warga 1
        $this->userWarga1 = Pengguna::create([
            'nama' => 'Budi Santoso',
            'username' => '1802011111110001',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567891',
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga1 = Warga::create([
            'id_pengguna' => $this->userWarga1->id_pengguna,
            'nik' => '1802011111110001',
            'no_kk' => '1802011111110000',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Mawar No. 1',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '001',
        ]);

        // Warga 2
        $this->userWarga2 = Pengguna::create([
            'nama' => 'Siti Aminah',
            'username' => '1802012222220002',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567892',
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga2 = Warga::create([
            'id_pengguna' => $this->userWarga2->id_pengguna,
            'nik' => '1802012222220002',
            'no_kk' => '1802012222220000',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1992-02-02',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Jl. Melati No. 2',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '002',
            'rw' => '001',
        ]);
    }

    public function test_halaman_riwayat_non_ajax_mengembalikan_halaman_penuh(): void
    {
        $response = $this->actingAs($this->userWarga1)->get(route('riwayat'));

        $response->assertOk();
        $response->assertViewIs('pengajuan.index');
        $response->assertSee('Riwayat Pengajuan Surat');
        $response->assertSee('area-hasil-riwayat');
    }

    public function test_halaman_riwayat_ajax_mengembalikan_fragmen_partial(): void
    {
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0001',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Melamar Kerja'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat'));

        $response->assertOk();
        $response->assertViewIs('pengajuan.partials.daftar-riwayat');
        $response->assertSee('PG-20261102-0001');
        $response->assertDontSee('<!DOCTYPE html>', false);
        $response->assertDontSee('<x-app-layout>', false);
    }

    public function test_pencarian_berdasarkan_nomor_pengajuan_via_ajax(): void
    {
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0010',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Urus Berkas'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0099',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSktm->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['tujuan' => 'Beasiswa'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => '0010']));

        $response->assertOk();
        $response->assertSee('PG-20261102-0010');
        $response->assertDontSee('PG-20261102-0099');
    }

    public function test_pencarian_berdasarkan_nomor_surat_resmi_via_ajax(): void
    {
        $kepala = Pengguna::create([
            'nama' => 'Kepala Desa',
            'username' => 'kepala',
            'password' => bcrypt('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        $pSelesai = Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0020',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'selesai',
            'data_isian' => ['keperluan' => 'Pendaftaran'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        Surat::create([
            'id_pengajuan' => $pSelesai->id_pengajuan,
            'no_surat' => '045/RH/SKCK/XI/2026',
            'tgl_surat' => '2026-11-02',
            'kode_verifikasi' => str_repeat('b', 32),
            'file_pdf' => 'surat/2026/test.pdf',
            'id_penandatangan' => $kepala->id_pengguna,
            'ditandatangani_at' => now(),
        ]);

        $pLain = Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0021',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSktm->id_layanan,
            'status' => 'diproses',
            'data_isian' => ['tujuan' => 'Bantuan'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => '045/RH/SKCK']));

        $response->assertOk();
        $response->assertSee('PG-20261102-0020');
        $response->assertSee('045/RH/SKCK/XI/2026');
        $response->assertDontSee('PG-20261102-0021');
    }

    public function test_pencarian_berdasarkan_nama_atau_kode_layanan_via_ajax(): void
    {
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0030',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Layanan SKCK'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0031',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSktm->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['tujuan' => 'Layanan SKTM'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => 'SKTM']));

        $response->assertOk();
        $response->assertSee('PG-20261102-0031');
        $response->assertDontSee('PG-20261102-0030');
    }

    public function test_pencarian_dengan_karakter_wildcard_like_terescape(): void
    {
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0100',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Uji Karakter Khusus'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        // Mengirimkan karakter '%' atau '_' tidak boleh mencocokkan sembarang data jika tidak ada yang cocok
        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => '%xyz%']));

        $response->assertOk();
        $response->assertSee('Belum Ada Permohonan Surat');
        $response->assertDontSee('PG-20261102-0100');
    }

    public function test_warga_tidak_dapat_melihat_pengajuan_milik_warga_lain_lewat_pencarian_ajax(): void
    {
        // Pengajuan milik warga 2
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-9999',
            'id_warga' => $this->warga2->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Rahasia Warga 2'],
            'id_pengubah' => $this->userWarga2->id_pengguna,
        ]);

        // Warga 1 mencari nomor pengajuan milik warga 2
        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => 'PG-20261102-9999']));

        $response->assertOk();
        $response->assertDontSee('PG-20261102-9999');
        $response->assertSee('Belum Ada Permohonan Surat');
    }

    public function test_kombinasi_pencarian_dengan_filter_status(): void
    {
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0051',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Filter SKCK'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        Pengajuan::create([
            'no_pengajuan' => 'PG-20261102-0052',
            'id_warga' => $this->warga1->id_warga,
            'id_layanan' => $this->layananSkck->id_layanan,
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Filter SKCK Diproses'],
            'id_pengubah' => $this->userWarga1->id_pengguna,
        ]);

        // Cari '005' dengan status 'menunggu'
        $response = $this->actingAs($this->userWarga1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('riwayat', ['q' => '005', 'status' => 'menunggu']));

        $response->assertOk();
        $response->assertSee('PG-20261102-0051');
        $response->assertDontSee('PG-20261102-0052');
    }

    public function test_akses_riwayat_oleh_role_bukan_warga_ditolak(): void
    {
        $kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan',
            'username' => 'kasi',
            'password' => bcrypt('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($kasi)->get(route('riwayat'));
        $response->assertForbidden();
    }
}

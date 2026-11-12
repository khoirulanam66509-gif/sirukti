<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Surat;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerifikasiSuratPublikTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kepala;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Layanan $layanan;

    protected Pengajuan $pengajuan;

    protected Surat $surat;

    protected function setUp(): void
    {
        parent::setUp();

        $dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        $this->kepala = Pengguna::create([
            'nama' => 'Bapak Kepala Desa',
            'username' => 'kepala_vrf_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'jabatan' => 'Kepala Kampung Rukti Harjo',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Siti Nurhaliza',
            'username' => '180201050595'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => '1802010505950001',
            'no_kk' => '1802010505950002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1995-05-05',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Jl. Merak No. 12',
            'id_dusun' => $dusun->id_dusun,
            'rt' => '002',
            'rw' => '001',
        ]);

        $this->layanan = Layanan::where('kode', 'SKTM')->first()
            ?? Layanan::firstOrCreate(
                ['kode' => 'SKTM'],
                ['nama_layanan' => 'Surat Keterangan Tidak Mampu', 'status_aktif' => true, 'urutan' => 1]
            );

        $this->pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'selesai',
            'data_isian' => ['keperluan' => 'Keringanan Biaya Rumah Sakit'],
            'tgl_selesai' => now(),
        ]);

        $this->surat = Surat::create([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'no_surat' => '088/RH/SKTM/X/2026',
            'tgl_surat' => now()->toDateString(),
            'kode_verifikasi' => Str::random(32),
            'file_pdf' => 'surat/2026/'.$this->pengajuan->no_pengajuan.'.pdf',
            'id_penandatangan' => $this->kepala->id_pengguna,
            'ditandatangani_at' => now(),
        ]);
    }

    public function test_halaman_verifikasi_dapat_diakses_publik_tanpa_login(): void
    {
        $response = $this->get(route('verifikasi.show', $this->surat->kode_verifikasi));

        $response->assertStatus(200);
        $response->assertViewIs('verifikasi.show');
    }

    public function test_verifikasi_dengan_kode_valid_menampilkan_status_sah_dan_rincian_dokumen(): void
    {
        $response = $this->get(route('verifikasi.show', $this->surat->kode_verifikasi));

        $response->assertStatus(200);
        $response->assertSee('Surat Terverifikasi');
        $response->assertSee('Sah &amp; Aktif', false);
        $response->assertSee($this->surat->no_surat);
        $response->assertSee($this->layanan->nama_layanan);
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee($this->kepala->nama_lengkap);
    }

    public function test_verifikasi_menampilkan_nik_pemohon_tersamarkan(): void
    {
        $response = $this->get(route('verifikasi.show', $this->surat->kode_verifikasi));

        $response->assertStatus(200);
        // NIK asli adalah 1802010505950001
        // Disamarkan: 180201******0001
        $response->assertSee('180201******0001');
        // Pastikan NIK utuh TIDAK muncul
        $response->assertDontSee('1802010505950001');
    }

    public function test_verifikasi_dengan_kode_tidak_dikenal_menampilkan_tampilan_tidak_valid(): void
    {
        $kodePalsu = 'kode-acak-palsu-1234567890abcdef';

        $response = $this->get(route('verifikasi.show', $kodePalsu));

        $response->assertStatus(200);
        $response->assertSee('Surat Tidak Ditemukan');
        $response->assertSee('Tidak Valid / Tidak Ditemukan');
        $response->assertSee('Tidak Terverifikasi');
        $response->assertSee($kodePalsu);
        $response->assertDontSee('Surat Terverifikasi');
    }

    public function test_verifikasi_surat_yang_pengajuannya_belum_selesai_menampilkan_tidak_valid(): void
    {
        $pengajuanBaru = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Belum Selesai'],
        ]);

        $suratDraft = Surat::create([
            'id_pengajuan' => $pengajuanBaru->id_pengajuan,
            'no_surat' => '099/RH/SKTM/X/2026',
            'tgl_surat' => now()->toDateString(),
            'kode_verifikasi' => Str::random(32),
            'file_pdf' => 'surat/2026/draft.pdf',
            'id_penandatangan' => $this->kepala->id_pengguna,
            'ditandatangani_at' => now(),
        ]);

        $response = $this->get(route('verifikasi.show', $suratDraft->kode_verifikasi));

        $response->assertStatus(200);
        $response->assertSee('Surat Tidak Ditemukan');
        $response->assertSee('Tidak Valid / Tidak Ditemukan');
        $response->assertDontSee('Surat Terverifikasi');
    }

    public function test_rate_limiting_pada_rute_verifikasi(): void
    {
        // Rute memiliki middleware throttle:60,1
        // Lakukan 60 request yang sukses
        for ($i = 0; $i < 60; $i++) {
            $response = $this->get(route('verifikasi.show', 'test-code-'.$i));
            $this->assertNotEquals(429, $response->status());
        }

        // Request ke-61 harus ditolak dengan status 429 Too Many Requests
        $response = $this->get(route('verifikasi.show', 'test-code-excess'));
        $response->assertStatus(429);
    }
}

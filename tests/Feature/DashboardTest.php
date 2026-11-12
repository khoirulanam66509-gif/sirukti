<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasi;

    protected Pengguna $kepala;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Layanan $layanan;

    protected Dusun $dusun;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I']);

        $this->kasi = Pengguna::create([
            'nama' => 'Ibu Siti Kasi',
            'username' => 'kasi_dash_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $this->kepala = Pengguna::create([
            'nama' => 'Bapak Kepala Desa',
            'username' => 'kepala_dash_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        $this->wargaUser = Pengguna::create([
            'nama' => 'Ahmad Warga',
            'username' => '180201010190'.rand(1000, 9999),
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => '1802010101900001',
            'no_kk' => '1802010101900002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Desa No. 1',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '001',
        ]);

        $this->layanan = Layanan::where('kode', 'SKCK')->first()
            ?? Layanan::firstOrCreate(
                ['kode' => 'SKCK'],
                ['nama_layanan' => 'Surat Pengantar SKCK', 'status_aktif' => true, 'urutan' => 1]
            );
    }

    public function test_tamu_diarahkan_ke_login_saat_mengakses_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_warga_melihat_beranda_w1_lengkap(): void
    {
        // Buat pengajuan aktif
        $pengajuanAktif = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Pendaftaran Kuliah'],
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.warga');
        $response->assertSee('Halo, Ahmad Warga');
        $response->assertSee('Ajukan Surat Keterangan Desa');
        $response->assertSee($pengajuanAktif->no_pengajuan);
        $response->assertSee('Sedang Diproses');
    }

    public function test_banner_profil_belum_lengkap_muncul_pada_warga_dengan_profil_kurang(): void
    {
        // Kosongkan alamat dan no_kk warga
        $this->warga->update([
            'no_kk' => null,
            'alamat' => null,
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kelengkapan Data Kependudukan');
        $response->assertSee('Lengkapi Profil');
    }

    public function test_kasi_melihat_beranda_k1_dengan_kartu_kpi_dan_antrean(): void
    {
        // Buat beberapa pengajuan dengan status berbeda
        Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Melamar Kerja'],
        ]);

        Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Keringanan Biaya'],
        ]);

        $response = $this->actingAs($this->kasi)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.kasi');
        $response->assertSee('Selamat bertugas, Ibu Siti Kasi');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Sedang Diproses');
        $response->assertSee('Menunggu TTD');
        $response->assertSee('Selesai Terbit');
        $response->assertSee('Antrean Pengajuan Surat Terbaru');
    }

    public function test_kepala_melihat_beranda_p1_dengan_kartu_ttd_dan_antrean_persetujuan(): void
    {
        $pengajuanTtd = Pengajuan::create([
            'no_pengajuan' => 'PG-'.now()->format('Ymd').'-'.rand(1000, 9999),
            'id_warga' => $this->warga->id_warga,
            'id_layanan' => $this->layanan->id_layanan,
            'tgl_pengajuan' => now(),
            'status' => 'menunggu_ttd',
            'data_isian' => ['keperluan' => 'Urusan Perbankan'],
        ]);

        $response = $this->actingAs($this->kepala)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.kepala');
        $response->assertSee('Persetujuan Naskah Dinas');
        $response->assertSee('Menunggu Tanda Tangan Digital');
        $response->assertSee('Surat Disetujui Bulan Ini');
        $response->assertSee($pengajuanTtd->no_pengajuan);
        $response->assertSee('Tinjau &amp; Sahkan', false);
    }
}

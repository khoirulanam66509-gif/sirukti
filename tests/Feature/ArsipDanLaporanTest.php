<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArsipDanLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_tamu_diarahkan_ke_login_saat_mengakses_arsip_laporan(): void
    {
        $response = $this->get('/arsip-laporan');
        $response->assertRedirect('/login');
    }

    public function test_warga_dilarang_mengakses_arsip_laporan(): void
    {
        $wargaUser = Pengguna::where('role', 'warga')->first();

        $response = $this->actingAs($wargaUser)->get('/arsip-laporan');
        $response->assertStatus(403);
    }

    public function test_kasi_dapat_mengakses_arsip_dan_melihat_rekapitulasi(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();

        $response = $this->actingAs($kasi)->get('/arsip-laporan');

        $response->assertStatus(200);
        $response->assertSee('Arsip');
        $response->assertSee('Statistik per Jenis Layanan');
        $response->assertSee('Daftar Arsip Pengajuan Surat');
        // Kasi melihat tombol Hapus Arsip
        $response->assertSee('Hapus Arsip Pengajuan?');
    }

    public function test_kepala_dapat_mengakses_arsip_secara_read_only(): void
    {
        $kepala = Pengguna::where('role', 'kepala')->first();

        $response = $this->actingAs($kepala)->get('/arsip-laporan');

        $response->assertStatus(200);
        $response->assertSee('Arsip');
        $response->assertSee('Cetak PDF');
        // Kepala tidak boleh melihat modal konfirmasi atau tombol hapus arsip
        $response->assertDontSee('title="Hapus Arsip (Soft Delete)"', false);
    }

    public function test_filter_periode_dan_layanan_berfungsi(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $layanan = Layanan::where('kode', 'SKCK')->first();

        $bulanSekarang = (int) date('n');
        $tahunSekarang = (int) date('Y');

        $response = $this->actingAs($kasi)->get("/arsip-laporan?bulan={$bulanSekarang}&tahun={$tahunSekarang}&id_layanan={$layanan->id_layanan}");

        $response->assertStatus(200);
        $response->assertSee($layanan->nama_layanan);
    }

    public function test_kasi_dan_kepala_dapat_mencetak_laporan_pdf(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $bulanSekarang = (int) date('n');
        $tahunSekarang = (int) date('Y');

        $response = $this->actingAs($kasi)->get("/arsip-laporan/cetak?bulan={$bulanSekarang}&tahun={$tahunSekarang}");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // Kepala juga dapat mencetak
        $kepala = Pengguna::where('role', 'kepala')->first();
        $responseKepala = $this->actingAs($kepala)->get("/arsip-laporan/cetak?bulan={$bulanSekarang}&tahun={$tahunSekarang}");
        $responseKepala->assertStatus(200);
        $this->assertEquals('application/pdf', $responseKepala->headers->get('content-type'));
    }

    public function test_kasi_dapat_melakukan_soft_delete_arsip_dan_data_keluar_dari_rekap(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $pengajuan = Pengajuan::first();

        $this->assertNotNull($pengajuan);
        $idPengajuan = $pengajuan->id_pengajuan;

        // Kasi melakukan DELETE
        $response = $this->actingAs($kasi)->delete("/arsip-laporan/{$idPengajuan}");

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Pastikan ter-soft-delete (deleted_at tidak null)
        $this->assertSoftDeleted('pengajuan', ['id_pengajuan' => $idPengajuan]);

        // Bersihkan flash session agar flash message tidak memuat no_pengajuan
        session()->forget('status');

        // Pengajuan tidak muncul di daftar index arsip aktif
        $indexResponse = $this->actingAs($kasi)->get('/arsip-laporan');
        $indexResponse->assertDontSee($pengajuan->no_pengajuan);
    }

    public function test_kepala_dilarang_menghapus_arsip(): void
    {
        $kepala = Pengguna::where('role', 'kepala')->first();
        $pengajuan = Pengajuan::first();

        $response = $this->actingAs($kepala)->delete("/arsip-laporan/{$pengajuan->id_pengajuan}");

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('pengajuan', ['id_pengajuan' => $pengajuan->id_pengajuan]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LacakPengajuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_halaman_lacak_dapat_diakses_publik_tanpa_login(): void
    {
        $response = $this->get('/lacak');

        $response->assertStatus(200);
        $response->assertSee('Lacak Status Permohonan Surat');
        $response->assertSee('Lacak Pengajuan');
    }

    public function test_pelacakan_dengan_nomor_valid_menampilkan_status_dan_data_pemohon(): void
    {
        $warga = Warga::first();
        $layanan = Layanan::first();

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261020-9999',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'status' => 'diproses',
            'data_isian' => ['keperluan' => 'Uji Coba Lacak Cepat'],
            'tgl_pengajuan' => now(),
        ]);

        $response = $this->get('/lacak?no_pengajuan=PG-20261020-9999');

        $response->assertStatus(200);
        $response->assertSee('PG-20261020-9999');
        $response->assertSee($warga->pengguna->nama);
        $response->assertSee(samarkan_nik($warga->nik));
        $response->assertSee('Diproses');
    }

    public function test_pelacakan_dengan_nomor_tidak_dikenal_menampilkan_notifikasi_tidak_ditemukan(): void
    {
        $response = $this->get('/lacak?no_pengajuan=PG-TIDAK-ADA-9999');

        $response->assertStatus(200);
        $response->assertSee('Nomor Pengajuan Tidak Ditemukan');
        $response->assertSee('PG-TIDAK-ADA-9999');
    }

    public function test_landing_page_menampilkan_widget_lacak_dan_hasil_pelacakan(): void
    {
        $warga = Warga::first();
        $layanan = Layanan::first();

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261020-7777',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Uji Coba Landing Lacak'],
            'tgl_pengajuan' => now(),
        ]);

        // Akses landing biasa
        $resLanding = $this->get('/');
        $resLanding->assertStatus(200);
        $resLanding->assertSee('Lacak Cepat Pengajuan');

        // Akses landing dengan parameter nomor pengajuan
        $resLacak = $this->get('/?no_pengajuan=PG-20261020-7777');
        $resLacak->assertStatus(200);
        $resLacak->assertSee('PG-20261020-7777');
        $resLacak->assertSee($warga->pengguna->nama);
        $resLacak->assertSee(samarkan_nik($warga->nik));
    }

    public function test_halaman_detail_kasi_memuat_tombol_wa_dan_salin_nomor(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $warga = Warga::first();
        $layanan = Layanan::first();

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261020-8888',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Uji Coba WA dan Salin'],
            'tgl_pengajuan' => now(),
        ]);

        $response = $this->actingAs($kasi)->get(route('kasi.pengajuan.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('PG-20261020-8888');
        $response->assertSee('salinTeks', false);
        $response->assertSee('Hubungi WA');
        $response->assertSee('https://wa.me/', false);
    }

    public function test_tombol_rincian_pada_hasil_lacak_menyesuaikan_peran_pengguna_saat_login(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();
        $warga = Warga::first();
        $layanan = Layanan::first();

        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261026-1414',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'status' => 'menunggu',
            'data_isian' => ['keperluan' => 'Uji Lacak Role Aware'],
            'tgl_pengajuan' => now(),
        ]);

        // Saat Kasi melacak di halaman publik /lacak, link rincian mengarah ke rute Kasi (bukan rute warga yang menyebabkan 403)
        $resKasi = $this->actingAs($kasi)->get('/lacak?no_pengajuan=PG-20261026-1414');
        $resKasi->assertStatus(200);
        $resKasi->assertSee(route('kasi.pengajuan.show', $pengajuan), false);

        // Saat Warga pemilik melacak di halaman publik /lacak, link rincian mengarah ke rute detail pengajuan warga
        $resWarga = $this->actingAs($warga->pengguna)->get('/lacak?no_pengajuan=PG-20261026-1414');
        $resWarga->assertStatus(200);
        $resWarga->assertSee(route('pengajuan.show', $pengajuan), false);
    }
}

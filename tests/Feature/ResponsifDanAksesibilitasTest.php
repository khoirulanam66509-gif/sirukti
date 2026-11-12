<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsifDanAksesibilitasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_seluruh_layout_memuat_meta_viewport_mobile_first(): void
    {
        // Landing Page
        $resLanding = $this->get('/');
        $resLanding->assertStatus(200);
        $resLanding->assertSee('name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"', false);

        // Halaman Login & Register
        $resLogin = $this->get('/login');
        $resLogin->assertStatus(200);
        $resLogin->assertSee('viewport-fit=cover', false);

        // Dashboard terautentikasi
        $warga = Pengguna::where('role', 'warga')->first();
        $resDash = $this->actingAs($warga)->get('/dashboard');
        $resDash->assertStatus(200);
        $resDash->assertSee('viewport-fit=cover', false);
    }

    public function test_form_registrasi_dan_profil_memuat_inputmode_dan_aksesibilitas(): void
    {
        // Register form
        $resReg = $this->get('/register');
        $resReg->assertStatus(200);
        $resReg->assertSee('inputmode="numeric"', false);
        $resReg->assertSee('inputmode="tel"', false);
        $resReg->assertSee('maxlength="16"', false);

        // Profil form
        $warga = Pengguna::where('role', 'warga')->first();
        $resProfil = $this->actingAs($warga)->get('/profil');
        $resProfil->assertStatus(200);
        $resProfil->assertSee('inputmode="numeric"', false);
        $resProfil->assertSee('maxlength="16"', false);
    }

    public function test_bottom_nav_tersedia_untuk_setiap_role_pada_layar_mobile(): void
    {
        // 1. Warga (memiliki menu bottom-nav & bottom-sheet menu profil + keluar)
        $warga = Pengguna::where('role', 'warga')->first();
        $resWarga = $this->actingAs($warga)->get('/dashboard');
        $resWarga->assertStatus(200);
        $resWarga->assertSee('Ajukan', false);
        $resWarga->assertSee('Riwayat', false);
        $resWarga->assertSee('Pengaturan Profil', false);
        $resWarga->assertSee('Ubah Password', false);
        $resWarga->assertSee('Keluar dari Akun', false);

        // 2. Kasi
        $kasi = Pengguna::where('role', 'kasi')->first();
        $resKasi = $this->actingAs($kasi)->get('/dashboard');
        $resKasi->assertStatus(200);
        $resKasi->assertSee('Antrean', false);
        $resKasi->assertSee('Arsip', false);

        // 3. Kepala
        $kepala = Pengguna::where('role', 'kepala')->first();
        $resKepala = $this->actingAs($kepala)->get('/dashboard');
        $resKepala->assertStatus(200);
        $resKepala->assertSee('Persetujuan', false);
        $resKepala->assertSee('Arsip', false);
    }

    public function test_empty_state_ditampilkan_saat_data_kosong(): void
    {
        $kasi = Pengguna::where('role', 'kasi')->first();

        // Cari dengan query kata kunci acak yang tidak mungkin cocok
        $res = $this->actingAs($kasi)->get('/arsip-laporan?q=ZZZ_NON_EXISTENT_QUERY_9999');
        $res->assertStatus(200);
        $res->assertSee('Tidak ada arsip pengajuan yang cocok dengan filter yang dipilih.');
    }

    public function test_halaman_error_404_responsif_dan_berbahasa_indonesia(): void
    {
        $res = $this->get('/halaman-yang-pasti-tidak-ada-404');
        $res->assertStatus(404);
        $res->assertSee('Halaman Tidak Ditemukan');
        $res->assertSee('Kembali ke Beranda');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use Database\Seeders\LayananSeeder;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LayananSeeder::class,
            PengaturanSeeder::class,
        ]);
    }

    public function test_landing_page_loads_successfully_with_http_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SIRUKTI');
        $response->assertSee('Pelayanan Surat Digital');
        $response->assertSee('Kampung Rukti Harjo');
    }

    public function test_landing_page_contains_indonesian_meta_tags_and_title(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<title>SIRUKTI — Pelayanan Surat Digital Kampung Rukti Harjo</title>', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('lang="id"', false);
    }

    public function test_landing_page_displays_all_four_active_services_and_their_requirements(): void
    {
        $layananList = Layanan::with('syaratAktif')
            ->where('status_aktif', true)
            ->orderBy('urutan')
            ->get();

        $this->assertCount(4, $layananList);

        $response = $this->get('/');

        foreach ($layananList as $layanan) {
            $response->assertSee($layanan->nama_layanan);
            $response->assertSee($layanan->deskripsi);

            foreach ($layanan->syaratAktif as $syarat) {
                $response->assertSee($syarat->nama_syarat);
            }
        }
    }

    public function test_landing_page_displays_statistics_from_pengaturan(): void
    {
        Pengaturan::simpan('statistik_kampung', [
            'jiwa' => 6120,
            'kk' => 1850,
            'surat_terbit' => 199,
            'sumber' => 'Uji Statistik Pengaturan',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('6.120 Jiwa');
        $response->assertSee('1.850 Kepala Keluarga');
        $response->assertSee('199 Surat Terbit');
    }

    public function test_landing_page_displays_operational_hours_and_whatsapp_link(): void
    {
        Pengaturan::simpan('profil_kampung', [
            'nama_kampung' => 'Rukti Harjo',
            'kecamatan' => 'Seputih Raman',
            'kabupaten' => 'Lampung Tengah',
            'alamat' => 'Jl. Uji Coba Balai No. 99',
            'kode_pos' => '34155',
            'telepon' => '089999999999',
            'whatsapp' => '628999999999',
        ]);

        Pengaturan::simpan('jam_layanan', [
            'senin_kamis' => '08.00 - 15.00 WIB',
            'jumat' => '08.00 - 11.30 WIB',
            'catatan' => 'Uji jam layanan',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('08.00 - 15.00 WIB');
        $response->assertSee('08.00 - 11.30 WIB');
        $response->assertSee('Jl. Uji Coba Balai No. 99');
        $response->assertSee('https://wa.me/628999999999', false);
    }

    public function test_landing_page_shows_guest_buttons_when_not_logged_in(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
        $response->assertSee('Masuk');
        $response->assertSee('Daftar Akun');
    }

    public function test_landing_page_shows_dashboard_link_when_logged_in(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Tester',
            'username' => '1802010101990099',
            'password' => Hash::make('password'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pengguna)->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee('Buka Beranda');
    }

    public function test_landing_page_includes_faq_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Berkas apa saja yang perlu disiapkan?');
        $response->assertSee('Berapa lama surat selesai?');
        $response->assertSee('Bagaimana jika berkas saya kurang?');
        $response->assertSee('Bagaimana cara memastikan surat asli?');
    }

    public function test_landing_page_renders_four_simplified_process_steps(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Warga');
        $response->assertSee('Ajukan & Unggah Berkas');
        $response->assertSee('Pantau Status Real-time');
        $response->assertSee('Unduh Surat Resmi');
    }
}

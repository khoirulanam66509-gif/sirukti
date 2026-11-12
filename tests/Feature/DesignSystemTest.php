<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_helpers_function_correctly(): void
    {
        // Test tgl_indo
        $date = '2026-10-19 14:30:00';
        $this->assertEquals('19 Oktober 2026', tgl_indo($date));
        $this->assertEquals('19 Oktober 2026, 14:30 WIB', tgl_indo($date, true));
        $this->assertEquals('-', tgl_indo(null));

        // Test rupiah
        $this->assertEquals('Rp 50.000', rupiah(50000));
        $this->assertEquals('Rp 0', rupiah(0));
        $this->assertEquals('Rp 0', rupiah(null));

        // Test samarkan_nik (format: 6 digit awal + 6 bintang + 4 digit akhir)
        $this->assertEquals('180201******0001', samarkan_nik('1802012345670001'));
        $this->assertEquals('', samarkan_nik(null));
        $this->assertEquals('1234', samarkan_nik('1234'));
    }

    public function test_design_system_preview_accessible_when_debug_enabled(): void
    {
        config(['app.debug' => true]);

        $response = $this->get('/design-system');
        $response->assertStatus(200);
        $response->assertSee('Pratinjau Sistem Desain');
        $response->assertSee('Lencana Status Pengajuan');
        $response->assertSee('Tombol Aksi');
        $response->assertSee('180201******0001');
        $response->assertSee('Rp 250.000');
    }

    public function test_design_system_preview_accessible_with_authenticated_user(): void
    {
        config(['app.debug' => true]);

        $pengguna = Pengguna::create([
            'username' => '1802012345670001',
            'password' => bcrypt('password123'),
            'role' => 'kasi',
            'nama' => 'Budi Pratama',
            'no_hp' => '081234567890',
            'aktif' => true,
            'wajib_ganti_password' => false,
        ]);

        $response = $this->actingAs($pengguna)->get('/design-system');
        $response->assertStatus(200);
        $response->assertSee('Budi Pratama');
        $response->assertSee('Kasi Pelayanan');
    }
}

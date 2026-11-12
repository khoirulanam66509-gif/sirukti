<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'role:kasi'])->get('/test-role-kasi', function () {
            return response('kasi-area');
        });

        Route::middleware(['web', 'role:kepala'])->get('/test-role-kepala', function () {
            return response('kepala-area');
        });

        Route::middleware(['web', 'role:warga'])->get('/test-role-warga', function () {
            return response('warga-area');
        });
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/test-role-kasi');

        $response->assertRedirect(route('login'));
    }

    public function test_warga_cannot_access_kasi_or_kepala_routes(): void
    {
        $warga = Pengguna::create([
            'nama' => 'Warga Tester',
            'username' => '1802051212990071',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($warga)->get('/test-role-kasi');
        $response->assertStatus(403);
        $response->assertSee('Akses Dibatasi Khusus Aparatur');

        $responseKepala = $this->actingAs($warga)->get('/test-role-kepala');
        $responseKepala->assertStatus(403);
    }

    public function test_kasi_can_access_kasi_route(): void
    {
        $kasi = Pengguna::create([
            'nama' => 'Kasi Pelayanan',
            'username' => 'kasi_tester_72',
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($kasi)->get('/test-role-kasi');
        $response->assertStatus(200);
        $response->assertSee('kasi-area');
    }

    public function test_kepala_can_access_kepala_route(): void
    {
        $kepala = Pengguna::create([
            'nama' => 'Kepala Kampung',
            'username' => 'kepala_tester_73',
            'password' => Hash::make('password123'),
            'role' => 'kepala',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($kepala)->get('/test-role-kepala');
        $response->assertStatus(200);
        $response->assertSee('kepala-area');
    }

    public function test_404_error_page_renders_with_indonesian_content(): void
    {
        $response = $this->get('/halaman-yang-pasti-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('SIRUKTI');
    }
}

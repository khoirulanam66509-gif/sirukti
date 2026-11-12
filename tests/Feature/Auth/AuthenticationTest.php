<?php

namespace Tests\Feature\Auth;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('NIK / Username');
        $response->assertSee('Kata Sandi');
        $response->assertDontSee('Ingat Saya');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Ahmad Warga',
            'username' => '1802051212990088',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'username' => '1802051212990088',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($pengguna);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        Pengguna::create([
            'nama' => 'Ahmad Warga',
            'username' => '1802051212990087',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $this->post('/login', [
            'username' => '1802051212990087',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_authenticate(): void
    {
        Pengguna::create([
            'nama' => 'Warga Nonaktif',
            'username' => '1802051212990086',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => false,
        ]);

        $response = $this->post('/login', [
            'username' => '1802051212990086',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_users_can_logout(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Keluar',
            'username' => '1802051212990085',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pengguna)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_make_petugas_artisan_command_creates_petugas_account(): void
    {
        $this->artisan('make:petugas', [
            'role' => 'kasi',
            'username' => 'kasi_baru_test',
            '--nama' => 'Bambang Kasi, S.Sos',
            '--password' => 'kasisakti123',
            '--jabatan' => 'Kasi Pelayanan Rukti Harjo',
        ])->assertSuccessful();

        $this->assertDatabaseHas('pengguna', [
            'username' => 'kasi_baru_test',
            'role' => 'kasi',
            'jabatan' => 'Kasi Pelayanan Rukti Harjo',
            'status_aktif' => true,
            'wajib_ganti_password' => false,
        ]);
    }
}

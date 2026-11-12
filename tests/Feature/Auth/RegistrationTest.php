<?php

namespace Tests\Feature\Auth;

use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
        $response->assertSee('Nomor Induk Kependudukan (NIK)');
    }

    public function test_new_warga_can_register_and_creates_pengguna_and_warga_in_transaction(): void
    {
        $nik = '1802051212990091';

        $response = $this->post('/register', [
            'nama' => 'Budi Santoso',
            'nik' => $nik,
            'no_hp' => '081234567891',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Verifikasi pengguna terbuat dengan role warga, no_hp, dan username = NIK
        $this->assertDatabaseHas('pengguna', [
            'nama' => 'Budi Santoso',
            'username' => $nik,
            'no_hp' => '081234567891',
            'role' => 'warga',
            'status_aktif' => true,
            'wajib_ganti_password' => false,
        ]);

        // Verifikasi warga terbuat berelasi dengan pengguna
        $pengguna = Pengguna::where('username', $nik)->first();
        $this->assertNotNull($pengguna);

        $this->assertDatabaseHas('warga', [
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => $nik,
        ]);
    }

    public function test_registration_fails_if_nik_is_invalid(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Budi Santoso',
            'nik' => '12345', // kurang dari 16 digit
            'no_hp' => '081234567891',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertGuest();
    }

    public function test_registration_fails_if_nik_already_registered(): void
    {
        $nik = '1802051212990092';

        Pengguna::create([
            'nama' => 'Warga Lama',
            'username' => $nik,
            'password' => bcrypt('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->post('/register', [
            'nama' => 'Warga Baru NIK Sama',
            'nik' => $nik,
            'no_hp' => '081234567892',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertGuest();
    }

    public function test_registration_fails_if_password_confirmation_does_not_match(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Budi Santoso',
            'nik' => '1802051212990093',
            'no_hp' => '081234567893',
            'password' => 'password123',
            'password_confirmation' => 'berbeda123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ubah_password_screen_can_be_rendered(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Sandi',
            'username' => '1802051212990081',
            'password' => Hash::make('password_lama123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pengguna)->get('/ubah-password');

        $response->assertStatus(200);
        $response->assertSee('Ubah Kata Sandi Akun');
        $response->assertSee('Kata Sandi Saat Ini');
        $response->assertSee('Kata Sandi Baru');
    }

    public function test_password_can_be_updated(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Sandi 2',
            'username' => '1802051212990082',
            'password' => Hash::make('password_lama123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pengguna)->put('/ubah-password', [
            'current_password' => 'password_lama123',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('password_baru123', $pengguna->fresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Sandi 3',
            'username' => '1802051212990083',
            'password' => Hash::make('password_lama123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pengguna)->put('/ubah-password', [
            'current_password' => 'wrong-current-password',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password_lama123', $pengguna->fresh()->password));
    }

    public function test_user_with_wajib_ganti_password_is_redirected_to_ubah_password(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Warga Wajib Ganti',
            'username' => '1802051212990084',
            'password' => Hash::make('sementara123'),
            'role' => 'warga',
            'status_aktif' => true,
            'wajib_ganti_password' => true,
        ]);

        // Coba akses dashboard
        $response = $this->actingAs($pengguna)->get('/dashboard');

        $response->assertRedirect(route('password.change'));

        // Ubah password
        $updateResponse = $this->actingAs($pengguna)->put('/ubah-password', [
            'current_password' => 'sementara123',
            'password' => 'permanen123',
            'password_confirmation' => 'permanen123',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertFalse((bool) $pengguna->fresh()->wajib_ganti_password);

        // Setelah diubah, akses dashboard diizinkan
        $nextResponse = $this->actingAs($pengguna->fresh())->get('/dashboard');
        $nextResponse->assertStatus(200);
    }
}

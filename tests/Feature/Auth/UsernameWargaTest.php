<?php

namespace Tests\Feature\Auth;

use App\Models\Dusun;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsernameWargaTest extends TestCase
{
    use DatabaseTransactions;

    protected Dusun $dusun;

    protected Pengguna $wargaUser;

    protected Warga $warga;

    protected Pengguna $kasiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::firstOrCreate(['nama_dusun' => 'Dusun I Test R2']);

        $nik = '180201'.rand(1000000000, 9999999999);

        // Pengguna warga default dengan username = NIK
        $this->wargaUser = Pengguna::create([
            'nama' => 'Budi Santoso',
            'username' => $nik,
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
            'no_hp' => '0812'.rand(10000000, 99999999),
        ]);

        $this->warga = Warga::create([
            'id_pengguna' => $this->wargaUser->id_pengguna,
            'nik' => $nik,
            'no_kk' => '180201'.rand(1000000000, 9999999999),
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => 1,
            'rw' => 1,
            'alamat' => 'Jl. Uji Username No. 1',
            'tempat_lahir' => 'Lampung',
            'tanggal_lahir' => '1995-05-05',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
        ]);

        // Pengguna Kasi
        $this->kasiUser = Pengguna::create([
            'nama' => 'Petugas Kasi R2',
            'username' => 'kasi_r2_'.uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'kasi',
            'status_aktif' => true,
        ]);
    }

    /**
     * Uji: Warga dapat login menggunakan NIK bawaan registrasi (username default = NIK).
     */
    public function test_warga_dapat_login_menggunakan_nik_bawaan(): void
    {
        $response = $this->post(route('login'), [
            'username' => $this->warga->nik,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->wargaUser);
    }

    /**
     * Uji: Warga dapat mengubah username di formulir profil kependudukan.
     */
    public function test_warga_dapat_mengubah_username_di_halaman_profil(): void
    {
        $newUsername = 'budi_santoso_'.rand(100, 999);

        $response = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => 'Budi Santoso Update',
                'username' => $newUsername,
                'no_hp' => $this->wargaUser->no_hp,
                'no_kk' => $this->warga->no_kk,
                'tempat_lahir' => 'Lampung',
                'tgl_lahir' => '1995-05-05',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_perkawinan' => 'kawin',
                'pekerjaan' => 'Wiraswasta',
                'alamat' => 'Jl. Mawar No. 5',
                'id_dusun' => $this->dusun->id_dusun,
                'rt' => '001',
                'rw' => '001',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertEquals(strtolower($newUsername), $this->wargaUser->fresh()->username);
    }

    /**
     * Uji: Setelah username diubah, login menggunakan username baru BERHASIL.
     */
    public function test_warga_dapat_login_menggunakan_username_baru(): void
    {
        $newUsername = 'budi_baru_'.rand(100, 999);
        $this->wargaUser->update(['username' => $newUsername]);

        $response = $this->post(route('login'), [
            'username' => $newUsername,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->wargaUser);
    }

    /**
     * Uji: Setelah username diubah, warga TETAP DAPAT login menggunakan NIK 16 digit miliknya.
     */
    public function test_warga_tetap_dapat_login_menggunakan_nik_meskipun_username_sudah_diubah(): void
    {
        $newUsername = 'warga_keren_'.rand(100, 999);
        $this->wargaUser->update(['username' => $newUsername]);

        // Login menggunakan NIK, bukan username
        $response = $this->post(route('login'), [
            'username' => $this->warga->nik,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->wargaUser);
    }

    /**
     * Uji: Login username bersifat case-insensitive (huruf besar/kecil tidak membedakan).
     */
    public function test_login_username_bersifat_case_insensitive(): void
    {
        $newUsername = 'budi_sukses_'.rand(100, 999);
        $this->wargaUser->update(['username' => strtolower($newUsername)]);

        // Coba login dengan kombinasi huruf kapital
        $response = $this->post(route('login'), [
            'username' => strtoupper($newUsername),
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->wargaUser);
    }

    /**
     * Uji: Validasi menolak karakter ilegal (selain huruf, angka, dan underscore).
     */
    public function test_validasi_menolak_karakter_ilegal_pada_username(): void
    {
        $invalidUsernames = ['budi-santoso', 'budi santoso', 'budi@123', 'budi#keren', 'budi.oke'];

        foreach ($invalidUsernames as $inv) {
            $response = $this->actingAs($this->wargaUser)
                ->patch(route('profile.update'), [
                    'nama' => $this->wargaUser->nama,
                    'username' => $inv,
                    'no_hp' => $this->wargaUser->no_hp,
                ]);

            $response->assertSessionHasErrors('username');
        }
    }

    /**
     * Uji: Validasi menolak username kurang dari 4 karakter atau lebih dari 30 karakter.
     */
    public function test_validasi_panjang_karakter_username(): void
    {
        // Kurang dari 4 karakter
        $responseShort = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => $this->wargaUser->nama,
                'username' => 'abc',
                'no_hp' => $this->wargaUser->no_hp,
            ]);
        $responseShort->assertSessionHasErrors('username');

        // Lebih dari 30 karakter
        $responseLong = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => $this->wargaUser->nama,
                'username' => str_repeat('a', 31),
                'no_hp' => $this->wargaUser->no_hp,
            ]);
        $responseLong->assertSessionHasErrors('username');
    }

    /**
     * Uji: Validasi menolak username yang sudah digunakan akun lain.
     */
    public function test_validasi_username_harus_unik(): void
    {
        $userLain = Pengguna::create([
            'nama' => 'Warga Lain',
            'username' => 'warga_duplikat',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => $this->wargaUser->nama,
                'username' => 'WARGA_DUPLIKAT', // uji case-insensitive unik
                'no_hp' => $this->wargaUser->no_hp,
            ]);

        $response->assertSessionHasErrors('username');
    }

    /**
     * Uji: Validasi menolak 16 digit angka jika bukan merupakan NIK miliknya sendiri.
     */
    public function test_validasi_menolak_16_digit_angka_bukan_nik_sendiri(): void
    {
        $nikOrangLain = '1802018888880009';

        $response = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => $this->wargaUser->nama,
                'username' => $nikOrangLain,
                'no_hp' => $this->wargaUser->no_hp,
            ]);

        $response->assertSessionHasErrors('username');
    }

    /**
     * Uji: Validasi mengizinkan 16 digit angka jika sama dengan NIK miliknya sendiri.
     */
    public function test_validasi_mengizinkan_16_digit_angka_jika_sama_dengan_nik_sendiri(): void
    {
        $response = $this->actingAs($this->wargaUser)
            ->patch(route('profile.update'), [
                'nama' => $this->wargaUser->nama,
                'username' => $this->warga->nik,
                'no_hp' => $this->wargaUser->no_hp,
            ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * Uji: Validasi menolak kata terlarang sistem (reserved keywords).
     */
    public function test_validasi_menolak_kata_terlarang(): void
    {
        $reserved = ['admin', 'kasi', 'kepala', 'root', 'system', 'sirukti'];

        foreach ($reserved as $kata) {
            $response = $this->actingAs($this->wargaUser)
                ->patch(route('profile.update'), [
                    'nama' => $this->wargaUser->nama,
                    'username' => $kata,
                    'no_hp' => $this->wargaUser->no_hp,
                ]);

            $response->assertSessionHasErrors('username');
        }
    }

    /**
     * Uji: Kasi dapat mengoreksi username warga di halaman Kelola Warga.
     */
    public function test_kasi_dapat_mengoreksi_username_warga(): void
    {
        $usernameKoreksi = 'budi_dikoreksi_'.rand(100, 999);

        $response = $this->actingAs($this->kasiUser)
            ->put(route('kasi.warga.update', $this->warga), [
                'nama' => 'Budi Santoso Koreksi',
                'username' => $usernameKoreksi,
                'no_hp' => $this->wargaUser->no_hp,
                'no_kk' => $this->warga->no_kk,
                'id_dusun' => $this->dusun->id_dusun,
                'rt' => '001',
                'rw' => '001',
            ]);

        $response->assertRedirect(route('kasi.warga.index'));
        $this->assertEquals(strtolower($usernameKoreksi), $this->wargaUser->fresh()->username);
    }
}

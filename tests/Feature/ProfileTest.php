<?php

namespace Tests\Feature;

use App\Models\Dusun;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private Dusun $dusun;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dusun = Dusun::create([
            'nama_dusun' => 'Dusun I A',
        ]);
    }

    private function createWargaUser(string $nik = '1802012501900001', string $noHp = '081234567890'): Pengguna
    {
        $user = Pengguna::create([
            'nama' => 'Sri Wahyuni',
            'username' => $nik,
            'password' => bcrypt('password123'),
            'no_hp' => $noHp,
            'role' => 'warga',
            'status_aktif' => true,
        ]);

        Warga::create([
            'id_pengguna' => $user->id_pengguna,
            'nik' => $nik,
        ]);

        return $user;
    }

    public function test_halaman_profil_memerlukan_autentikasi(): void
    {
        $response = $this->get('/profil');

        $response->assertRedirect('/login');
    }

    public function test_rute_profile_dialihkan_ke_profil(): void
    {
        $user = $this->createWargaUser();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertRedirect('/profil');
    }

    public function test_halaman_profil_dapat_ditampilkan_untuk_warga_yang_login(): void
    {
        $user = $this->createWargaUser();

        $response = $this->actingAs($user)->get('/profil');

        $response->assertOk();
        $response->assertSee('Profil Saya');
        $response->assertSee('1802012501900001');
        $response->assertSee('Terkunci Sistem');
        $response->assertSee('Tingkat Pengisian');
    }

    public function test_warga_dapat_memperbarui_profil_dengan_data_valid(): void
    {
        $user = $this->createWargaUser();

        $payload = [
            'nama' => 'Sri Wahyuni S.Pd',
            'no_hp' => '081298765432',
            'no_kk' => '1802012501900002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-05-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Guru',
            'alamat' => 'Jl. Merdeka No. 12',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '3',
            'rw' => '1',
        ];

        $response = $this->actingAs($user)->patch('/profil', $payload);

        $response->assertRedirect('/profil');
        $response->assertSessionHas('success');

        // Pastikan tabel pengguna terupdate
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'Sri Wahyuni S.Pd',
            'no_hp' => '081298765432',
        ]);

        // Pastikan tabel warga terupdate dan RT/RW diformat 3 digit (003, 001)
        $this->assertDatabaseHas('warga', [
            'id_pengguna' => $user->id_pengguna,
            'nik' => '1802012501900001',
            'no_kk' => '1802012501900002',
            'tempat_lahir' => 'Lampung Tengah',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Guru',
            'alamat' => 'Jl. Merdeka No. 12',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '003',
            'rw' => '001',
        ]);
    }

    public function test_nik_tidak_dapat_diubah_oleh_warga(): void
    {
        $user = $this->createWargaUser('1802012501900001');

        $payload = [
            'nama' => 'Sri Wahyuni',
            'no_hp' => '081234567890',
            'nik' => '1802019999999999', // mencoba mengganti NIK
            'no_kk' => '1802012501900002',
        ];

        $response = $this->actingAs($user)->patch('/profil', $payload);

        $response->assertRedirect('/profil');

        // NIK di database harus tetap 1802012501900001
        $this->assertDatabaseHas('warga', [
            'id_pengguna' => $user->id_pengguna,
            'nik' => '1802012501900001',
        ]);

        $this->assertDatabaseMissing('warga', [
            'nik' => '1802019999999999',
        ]);
    }

    public function test_warga_tidak_dapat_mengubah_profil_warga_lain(): void
    {
        $userA = $this->createWargaUser('1802012501900001', '081200000001');
        $userB = $this->createWargaUser('1802012501900002', '081200000002');

        $wargaB = $userB->warga;
        $wargaB->update([
            'alamat' => 'Alamat Asli Warga B',
        ]);

        // Warga A mencoba inject id_warga atau id_pengguna milik Warga B
        $payload = [
            'id_warga' => $wargaB->id_warga,
            'id_pengguna' => $userB->id_pengguna,
            'nama' => 'Nama Baru Warga A',
            'no_hp' => '081200000001',
            'alamat' => 'Alamat Diserang',
        ];

        $response = $this->actingAs($userA)->patch('/profil', $payload);

        $response->assertRedirect('/profil');

        // Data warga B harus tetap aman
        $this->assertDatabaseHas('warga', [
            'id_warga' => $wargaB->id_warga,
            'alamat' => 'Alamat Asli Warga B',
        ]);

        // Data warga A yang terupdate
        $this->assertDatabaseHas('warga', [
            'id_pengguna' => $userA->id_pengguna,
            'alamat' => 'Alamat Diserang',
        ]);
    }

    public function test_validasi_input_profil_warga(): void
    {
        $user = $this->createWargaUser('1802012501900001', '081200000001');
        $otherUser = $this->createWargaUser('1802012501900002', '081200000002');

        // Nama kosong
        $response = $this->actingAs($user)->patch('/profil', ['nama' => '']);
        $response->assertSessionHasErrors(['nama']);

        // No HP sudah terpakai pengguna lain
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000002', // nomor milik otherUser
        ]);
        $response->assertSessionHasErrors(['no_hp']);

        // No KK bukan 16 digit angka
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'no_kk' => '12345ABCDE',
        ]);
        $response->assertSessionHasErrors(['no_kk']);

        // Tanggal lahir masa depan
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'tgl_lahir' => now()->addDays(2)->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors(['tgl_lahir']);

        // Jenis kelamin tidak valid
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'jenis_kelamin' => 'X',
        ]);
        $response->assertSessionHasErrors(['jenis_kelamin']);

        // Agama tidak valid
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'agama' => 'AgamaAlien',
        ]);
        $response->assertSessionHasErrors(['agama']);

        // Status perkawinan tidak valid
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'status_perkawinan' => 'lajang_pilihan',
        ]);
        $response->assertSessionHasErrors(['status_perkawinan']);

        // Dusun tidak ada di DB
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'id_dusun' => 9999,
        ]);
        $response->assertSessionHasErrors(['id_dusun']);

        // RT / RW bukan angka atau lebih dari 3 digit
        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Sri',
            'no_hp' => '081200000001',
            'rt' => '1234',
            'rw' => 'ABC',
        ]);
        $response->assertSessionHasErrors(['rt', 'rw']);
    }

    public function test_indikator_dan_metode_kelengkapan_profil(): void
    {
        $user = $this->createWargaUser();
        $warga = $user->warga;

        // Kondisi awal (baru daftar): belum lengkap
        $this->assertFalse($warga->profilLengkap());
        $this->assertEquals(0, $warga->persentaseKelengkapan());
        $this->assertCount(11, $warga->kolomBelumLengkap());

        // Isi sebagian kolom
        $warga->update([
            'no_kk' => '1802012501900002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-05-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
        ]);

        $this->assertFalse($warga->fresh()->profilLengkap());
        $this->assertEquals(45, $warga->fresh()->persentaseKelengkapan()); // 5 / 11 = 45%
        $this->assertCount(6, $warga->fresh()->kolomBelumLengkap());

        // Lengkapi seluruh kolom
        $warga->update([
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Poros No. 1',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '002',
        ]);

        $this->assertTrue($warga->fresh()->profilLengkap());
        $this->assertEquals(100, $warga->fresh()->persentaseKelengkapan());
        $this->assertEmpty($warga->fresh()->kolomBelumLengkap());
    }

    public function test_warga_diarahkan_ke_profil_bila_mencoba_mengajukan_surat_sebelum_profil_lengkap(): void
    {
        $user = $this->createWargaUser();

        // Profil belum lengkap: akses rute pengajuan harus diarahkan ke /profil dengan pesan warning
        $response = $this->actingAs($user)->get('/pengajuan/buat');

        $response->assertRedirect('/profil');
        $response->assertSessionHas('warning', 'Mohon lengkapi profil kependudukan Anda terlebih dahulu sebelum mengajukan permohonan surat.');

        // Lengkapi profil
        $user->warga->update([
            'no_kk' => '1802012501900002',
            'tempat_lahir' => 'Lampung Tengah',
            'tgl_lahir' => '1990-05-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status_perkawinan' => 'kawin',
            'pekerjaan' => 'Petani',
            'alamat' => 'Jl. Poros No. 1',
            'id_dusun' => $this->dusun->id_dusun,
            'rt' => '001',
            'rw' => '002',
        ]);

        // Setelah lengkap, akses pengajuan diizinkan
        $response = $this->actingAs($user)->get('/pengajuan/buat');

        $response->assertOk();
        $response->assertSee('Ajukan Surat Baru');
    }

    public function test_halaman_profil_memuat_keamanan_akun_dan_elemen_perbaikan_10(): void
    {
        $user = $this->createWargaUser();

        $response = $this->actingAs($user)->get('/profil');

        $response->assertOk();
        // Kartu Keamanan Akun & Ubah Kata Sandi & Keluar
        $response->assertSee('Keamanan Akun');
        $response->assertSee('Ubah Kata Sandi');
        $response->assertSee(route('password.change'));
        $response->assertSee('Keluar dari Akun');
        $response->assertSee(route('logout'));

        // Kartu placeholder foto salinan KTP & KK telah dihapus
        $response->assertDontSee('Foto Salinan KTP & KK');

        // Catatan ringkas kerahasiaan ada di bawah form
        $response->assertSee('Data kependudukan Anda dilindungi dan hanya digunakan untuk pelayanan administrasi resmi Kampung Rukti Harjo.');

        // Bottom nav tetap tampil pada halaman profil
        $response->assertSee('Ajukan');
        $response->assertSee('Riwayat');
    }
}

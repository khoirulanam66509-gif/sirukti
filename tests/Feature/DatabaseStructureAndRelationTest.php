<?php

namespace Tests\Feature;

use App\Models\BerkasPengajuan;
use App\Models\Dusun;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\SyaratLayanan;
use App\Models\Warga;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseStructureAndRelationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_11_tables_exist(): void
    {
        $tables = [
            'dusun',
            'pengguna',
            'warga',
            'layanan',
            'syarat_layanan',
            'pengajuan',
            'berkas_pengajuan',
            'status_log',
            'counter_surat',
            'surat',
            'pengaturan',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} harus ada di database.");
        }
    }

    public function test_warga_profil_lengkap_method(): void
    {
        $dusun = Dusun::create(['nama_dusun' => 'Dusun Test']);
        $pengguna = Pengguna::create([
            'nama' => 'Test User',
            'username' => '1802010101990001',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        $wargaIncomplete = Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '1802010101990001',
            'tempat_lahir' => null,
        ]);

        $this->assertFalse($wargaIncomplete->profilLengkap());

        $wargaIncomplete->update([
            'no_kk' => '1802010101990002',
            'tempat_lahir' => 'Lampung',
            'tgl_lahir' => '1999-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'belum_kawin',
            'pekerjaan' => 'Mahasiswa',
            'alamat' => 'Jl. Test No. 1',
            'id_dusun' => $dusun->id_dusun,
            'rt' => '001',
            'rw' => '001',
        ]);

        $this->assertTrue($wargaIncomplete->fresh()->profilLengkap());
    }

    public function test_check_constraint_rejects_invalid_nik(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Invalid NIK User',
            'username' => 'invalid_nik',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        $this->expectException(QueryException::class);

        Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '12345', // kurang dari 16 digit
        ]);
    }

    public function test_check_constraint_rejects_invalid_no_kk(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Invalid KK User',
            'username' => '1802010101990099',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);

        $this->expectException(QueryException::class);

        Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '1802010101990099',
            'no_kk' => 'ABC12345', // bukan angka
        ]);
    }

    public function test_check_constraint_rejects_zero_ukuran_kb(): void
    {
        $dusun = Dusun::create(['nama_dusun' => 'Dusun Ukuran']);
        $pengguna = Pengguna::create([
            'nama' => 'User Ukuran',
            'username' => '1802010101990077',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);
        $warga = Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '1802010101990077',
        ]);
        $layanan = Layanan::create([
            'kode' => 'TST',
            'nama_layanan' => 'Layanan Test',
            'urutan' => 1,
        ]);
        $syarat = SyaratLayanan::create([
            'id_layanan' => $layanan->id_layanan,
            'nama_syarat' => 'KTP',
            'wajib' => true,
        ]);
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-9999',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test'],
            'id_pengubah' => $pengguna->id_pengguna,
        ]);

        $this->expectException(QueryException::class);

        BerkasPengajuan::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_syarat' => $syarat->id_syarat,
            'nama_file_asli' => 'ktp.jpg',
            'path_file' => 'test/ktp.jpg',
            'mime' => 'image/jpeg',
            'ukuran_kb' => 0, // CHECK ukuran_kb > 0
        ]);
    }

    public function test_fk_restrict_prevents_deleting_warga_with_pengajuan(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Restrict User',
            'username' => '1802010101990055',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);
        $warga = Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '1802010101990055',
        ]);
        $layanan = Layanan::create([
            'kode' => 'RST',
            'nama_layanan' => 'Restrict Service',
            'urutan' => 1,
        ]);
        Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-8888',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test Restrict'],
            'id_pengubah' => $pengguna->id_pengguna,
        ]);

        $this->expectException(QueryException::class);
        $warga->delete(); // Harusnya gagal karena FK restrict di pengajuan.id_warga
    }

    public function test_soft_delete_on_pengajuan(): void
    {
        $pengguna = Pengguna::create([
            'nama' => 'Soft User',
            'username' => '1802010101990044',
            'password' => Hash::make('password'),
            'role' => 'warga',
        ]);
        $warga = Warga::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'nik' => '1802010101990044',
        ]);
        $layanan = Layanan::create([
            'kode' => 'SFT',
            'nama_layanan' => 'Soft Service',
            'urutan' => 1,
        ]);
        $pengajuan = Pengajuan::create([
            'no_pengajuan' => 'PG-20261019-7777',
            'id_warga' => $warga->id_warga,
            'id_layanan' => $layanan->id_layanan,
            'data_isian' => ['keperluan' => 'Test Soft Delete'],
            'id_pengubah' => $pengguna->id_pengguna,
        ]);

        $pengajuan->delete();

        $this->assertSoftDeleted('pengajuan', ['id_pengajuan' => $pengajuan->id_pengajuan]);
        $this->assertNull(Pengajuan::find($pengajuan->id_pengajuan));
        $this->assertNotNull(Pengajuan::withTrashed()->find($pengajuan->id_pengajuan));
    }
}

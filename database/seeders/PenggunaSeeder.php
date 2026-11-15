<?php

namespace Database\Seeders;

use App\Models\Dusun;
use App\Models\Pengguna;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('password123');

        // 1. Akun Kasi Pelayanan
        Pengguna::updateOrCreate(
            ['username' => 'kasi'],
            [
                'nama' => 'Budi Santoso, S.IP',
                'password' => $passwordHash,
                'no_hp' => '081234567801',
                'role' => 'kasi',
                'jabatan' => 'Kasi Pelayanan',
                'nip' => '198205122008011003',
                'status_aktif' => true,
                'wajib_ganti_password' => true,
            ]
        );

        // 2. Akun Kepala Kampung
        Pengguna::updateOrCreate(
            ['username' => 'kepala'],
            [
                'nama' => 'I Wayan Sukadana',
                'password' => $passwordHash,
                'no_hp' => '081234567802',
                'role' => 'kepala',
                'jabatan' => 'Kepala Kampung',
                'nip' => '197508152005011002',
                'file_ttd' => 'ttd/z6hRYMdmKkgW9JuyhBGlxQKnvsKE1BNU08sFGM4H.png',
                'file_stempel' => 'stempel/7IJdzoaxVSTNYspz9zYipXUxoe9LTr8Fft89PeLE.png',
                'status_aktif' => true,
                'wajib_ganti_password' => true,
            ]
        );

        // 3. Warga Inti (Anam)
        $dusunList = Dusun::all();
        $idDusunDefault = $dusunList->first()?->id_dusun ?? 1;

        $daftarWarga = [
            [
                'username' => 'anam',
                'nama' => 'Khoirul Anam',
                'nik' => '1234567891011121',
                'no_kk' => '1234567891011120',
                'no_hp' => '081234567803',
                'tempat_lahir' => 'Lampung Tengah',
                'tgl_lahir' => '2000-01-01',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_perkawinan' => 'belum_kawin',
                'pekerjaan' => 'Belum Bekerja',
                'alamat' => 'RT 001 / RW 001',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun I A')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '001',
                'rw' => '001',
            ],
        ];

        foreach ($daftarWarga as $item) {
            $pengguna = Pengguna::updateOrCreate(
                ['username' => $item['username']],
                [
                    'nama' => $item['nama'],
                    'password' => $passwordHash,
                    'no_hp' => $item['no_hp'],
                    'role' => 'warga',
                    'status_aktif' => true,
                    'wajib_ganti_password' => false,
                ]
            );

            Warga::updateOrCreate(
                ['id_pengguna' => $pengguna->id_pengguna],
                [
                    'nik' => $item['nik'],
                    'no_kk' => $item['no_kk'],
                    'tempat_lahir' => $item['tempat_lahir'],
                    'tgl_lahir' => $item['tgl_lahir'],
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'agama' => $item['agama'],
                    'status_perkawinan' => $item['status_perkawinan'],
                    'pekerjaan' => $item['pekerjaan'],
                    'alamat' => $item['alamat'],
                    'id_dusun' => $item['id_dusun'],
                    'rt' => $item['rt'],
                    'rw' => $item['rw'],
                ]
            );
        }
    }
}

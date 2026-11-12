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
                'wajib_ganti_password' => false,
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
                'file_ttd' => 'ttd/dummy_ttd_kepala.png',
                'status_aktif' => true,
                'wajib_ganti_password' => false,
            ]
        );

        // 3. Warga Dummy (8 orang)
        $dusunList = Dusun::all();
        $idDusunDefault = $dusunList->first()?->id_dusun ?? 1;

        $daftarWarga = [
            [
                'nama' => 'Ahmad Fauzi',
                'nik' => '1802010101900001',
                'no_kk' => '1802010101900000',
                'no_hp' => '085211112221',
                'tempat_lahir' => 'Lampung Tengah',
                'tgl_lahir' => '1990-01-15',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_perkawinan' => 'kawin',
                'pekerjaan' => 'Petani',
                'alamat' => 'RT 001 / RW 001',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun I A')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '001',
                'rw' => '001',
            ],
            [
                'nama' => 'Siti Aminah',
                'nik' => '1802010202920002',
                'no_kk' => '1802010202920000',
                'no_hp' => '085211112222',
                'tempat_lahir' => 'Seputih Raman',
                'tgl_lahir' => '1992-05-20',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_perkawinan' => 'kawin',
                'pekerjaan' => 'Pedagang',
                'alamat' => 'RT 002 / RW 001',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun I B')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '002',
                'rw' => '001',
            ],
            [
                'nama' => 'I Made Suwarta',
                'nik' => '1802010303880003',
                'no_kk' => '1802010303880000',
                'no_hp' => '085211112223',
                'tempat_lahir' => 'Rukti Harjo',
                'tgl_lahir' => '1988-08-17',
                'jenis_kelamin' => 'L',
                'agama' => 'Hindu',
                'status_perkawinan' => 'kawin',
                'pekerjaan' => 'Wiraswasta',
                'alamat' => 'RT 003 / RW 002',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun II')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '003',
                'rw' => '002',
            ],
            [
                'nama' => 'Ni Luh Putu Astuti',
                'nik' => '1802010404950004',
                'no_kk' => '1802010404950000',
                'no_hp' => '085211112224',
                'tempat_lahir' => 'Rukti Harjo',
                'tgl_lahir' => '1995-12-05',
                'jenis_kelamin' => 'P',
                'agama' => 'Hindu',
                'status_perkawinan' => 'belum_kawin',
                'pekerjaan' => 'Guru Honorer',
                'alamat' => 'RT 001 / RW 003',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun III')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '001',
                'rw' => '003',
            ],
            [
                'nama' => 'Joko Widodo Prasetyo',
                'nik' => '1802010505850005',
                'no_kk' => '1802010505850000',
                'no_hp' => '085211112225',
                'tempat_lahir' => 'Solo',
                'tgl_lahir' => '1985-03-10',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_perkawinan' => 'kawin',
                'pekerjaan' => 'Buruh Harian Lepas',
                'alamat' => 'RT 002 / RW 004',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun IV')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '002',
                'rw' => '004',
            ],
            [
                'nama' => 'Maria Kristina',
                'nik' => '1802010606930006',
                'no_kk' => '1802010606930000',
                'no_hp' => '085211112226',
                'tempat_lahir' => 'Metro',
                'tgl_lahir' => '1993-07-25',
                'jenis_kelamin' => 'P',
                'agama' => 'Katolik',
                'status_perkawinan' => 'belum_kawin',
                'pekerjaan' => 'Karyawan Swasta',
                'alamat' => 'RT 001 / RW 005',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun V A')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '001',
                'rw' => '005',
            ],
            [
                'nama' => 'Bambang Triyono',
                'nik' => '1802010707790007',
                'no_kk' => '1802010707790000',
                'no_hp' => '085211112227',
                'tempat_lahir' => 'Lampung Tengah',
                'tgl_lahir' => '1979-11-12',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_perkawinan' => 'cerai_hidup',
                'pekerjaan' => 'Peternak',
                'alamat' => 'RT 003 / RW 006',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun VI')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '003',
                'rw' => '006',
            ],
            [
                'nama' => 'Dewi Lestari',
                'nik' => '1802010808980008',
                'no_kk' => '1802010808980000',
                'no_hp' => '085211112228',
                'tempat_lahir' => 'Seputih Raman',
                'tgl_lahir' => '1998-09-09',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_perkawinan' => 'belum_kawin',
                'pekerjaan' => 'Belum/Tidak Bekerja',
                'alamat' => 'RT 002 / RW 007',
                'id_dusun' => $dusunList->where('nama_dusun', 'Dusun VII')->first()?->id_dusun ?? $idDusunDefault,
                'rt' => '002',
                'rw' => '007',
            ],
        ];

        foreach ($daftarWarga as $item) {
            $pengguna = Pengguna::updateOrCreate(
                ['username' => $item['nik']],
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

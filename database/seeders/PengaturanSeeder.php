<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPengaturan = [
            'profil_kampung' => [
                'nama_kampung' => 'Rukti Harjo',
                'kecamatan' => 'Seputih Raman',
                'kabupaten' => 'Lampung Tengah',
                'alamat' => 'Jl. Merdeka No. 1, Kampung Rukti Harjo',
                'kode_pos' => '34155',
                'telepon' => '081234567890',
                'whatsapp' => '6281234567890',
            ],
            'jam_layanan' => [
                'senin_kamis' => '08.00 - 15.00 WIB',
                'jumat' => '08.00 - 11.30 WIB',
                'catatan' => 'Pelayanan administrasi offline dilayani pada jam kantor, permohonan daring aktif 24 jam.',
            ],
            'statistik_kampung' => [
                'jiwa' => 5990,
                'kk' => 1780,
                'sumber' => 'Laporan Bulanan Kependudukan September 2025',
            ],
            'surat' => [
                'kode_kampung' => 'RH',
                'jabatan_penandatangan' => 'Kepala Kampung',
            ],
        ];

        foreach ($dataPengaturan as $kunci => $nilai) {
            Pengaturan::updateOrCreate(
                ['kunci' => $kunci],
                ['nilai' => $nilai]
            );
        }
    }
}

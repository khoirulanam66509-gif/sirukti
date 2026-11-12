<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\SyaratLayanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarLayanan = [
            [
                'id_layanan' => 1,
                'kode' => 'SKCK',
                'nama_layanan' => 'Pengantar SKCK',
                'deskripsi' => 'Surat pengantar untuk mengurus SKCK di kepolisian',
                'status_aktif' => true,
                'urutan' => 1,
                'syarat' => [
                    ['nama_syarat' => 'Foto KTP', 'petunjuk' => 'Foto KTP asli, seluruh bagian terlihat dan terbaca', 'wajib' => true, 'urutan' => 1],
                    ['nama_syarat' => 'Foto KK', 'petunjuk' => 'Foto Kartu Keluarga, seluruh bagian terlihat', 'wajib' => true, 'urutan' => 2],
                    ['nama_syarat' => 'Pengantar RT/RW', 'petunjuk' => 'Foto surat pengantar dari RT/RW', 'wajib' => true, 'urutan' => 3],
                ],
            ],
            [
                'id_layanan' => 2,
                'kode' => 'DOM',
                'nama_layanan' => 'Keterangan Domisili',
                'deskripsi' => 'Keterangan tempat tinggal warga',
                'status_aktif' => true,
                'urutan' => 2,
                'syarat' => [
                    ['nama_syarat' => 'Foto KTP', 'petunjuk' => 'Foto KTP asli, seluruh bagian terlihat dan terbaca', 'wajib' => true, 'urutan' => 1],
                    ['nama_syarat' => 'Foto KK', 'petunjuk' => 'Foto Kartu Keluarga, seluruh bagian terlihat', 'wajib' => true, 'urutan' => 2],
                    ['nama_syarat' => 'Pengantar RT/RW', 'petunjuk' => 'Foto surat pengantar dari RT/RW', 'wajib' => true, 'urutan' => 3],
                ],
            ],
            [
                'id_layanan' => 3,
                'kode' => 'SKTM',
                'nama_layanan' => 'Keterangan Tidak Mampu',
                'deskripsi' => 'Keterangan kondisi ekonomi untuk keperluan sekolah/kesehatan/lainnya',
                'status_aktif' => true,
                'urutan' => 3,
                'syarat' => [
                    ['nama_syarat' => 'Foto KTP', 'petunjuk' => 'Foto KTP asli, seluruh bagian terlihat dan terbaca', 'wajib' => true, 'urutan' => 1],
                    ['nama_syarat' => 'Foto KK', 'petunjuk' => 'Foto Kartu Keluarga, seluruh bagian terlihat', 'wajib' => true, 'urutan' => 2],
                    ['nama_syarat' => 'Pengantar RT/RW', 'petunjuk' => 'Foto surat pengantar dari RT/RW', 'wajib' => true, 'urutan' => 3],
                ],
            ],
            [
                'id_layanan' => 4,
                'kode' => 'SKU',
                'nama_layanan' => 'Keterangan Usaha',
                'deskripsi' => 'Keterangan bahwa warga memiliki usaha',
                'status_aktif' => true,
                'urutan' => 4,
                'syarat' => [
                    ['nama_syarat' => 'Foto KTP', 'petunjuk' => 'Foto KTP asli, seluruh bagian terlihat dan terbaca', 'wajib' => true, 'urutan' => 1],
                    ['nama_syarat' => 'Foto KK', 'petunjuk' => 'Foto Kartu Keluarga, seluruh bagian terlihat', 'wajib' => true, 'urutan' => 2],
                    ['nama_syarat' => 'Pengantar RT/RW', 'petunjuk' => 'Foto surat pengantar dari RT/RW', 'wajib' => true, 'urutan' => 3],
                    ['nama_syarat' => 'Foto Tempat Usaha', 'petunjuk' => 'Foto tempat usaha yang jelas dari depan', 'wajib' => true, 'urutan' => 4],
                ],
            ],
        ];

        foreach ($daftarLayanan as $data) {
            $syaratList = $data['syarat'];
            unset($data['syarat']);

            $layanan = Layanan::updateOrCreate(['id_layanan' => $data['id_layanan']], $data);

            foreach ($syaratList as $syarat) {
                SyaratLayanan::updateOrCreate(
                    [
                        'id_layanan' => $layanan->id_layanan,
                        'nama_syarat' => $syarat['nama_syarat'],
                    ],
                    [
                        'petunjuk' => $syarat['petunjuk'],
                        'wajib' => $syarat['wajib'],
                        'urutan' => $syarat['urutan'],
                        'status_aktif' => true,
                    ]
                );
            }
        }
    }
}

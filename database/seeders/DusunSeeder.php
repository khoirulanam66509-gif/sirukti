<?php

namespace Database\Seeders;

use App\Models\Dusun;
use Illuminate\Database\Seeder;

class DusunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarDusun = [
            'Dusun I A',
            'Dusun I B',
            'Dusun II',
            'Dusun III',
            'Dusun IV',
            'Dusun V A',
            'Dusun V B',
            'Dusun VI',
            'Dusun VII',
            'Dusun VIII',
            'Dusun IX',
        ];

        foreach ($daftarDusun as $nama) {
            Dusun::firstOrCreate(['nama_dusun' => $nama]);
        }
    }
}

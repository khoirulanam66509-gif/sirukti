<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DusunSeeder::class,
            LayananSeeder::class,
            PengaturanSeeder::class,
            PenggunaSeeder::class,
            PengajuanSeeder::class,
        ]);
    }
}

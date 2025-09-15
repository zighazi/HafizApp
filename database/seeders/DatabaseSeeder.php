<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AngkatanSeeder::class,
            KelasSeeder::class,
            StrataSeeder::class,
            SantriSampleSeeder::class,
            // Tambahkan seeder lain di sini bila perlu, mis. SurahSeeder::class
        ]);
    }
}

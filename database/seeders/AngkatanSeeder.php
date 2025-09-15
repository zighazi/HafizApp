<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AngkatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('angkatan')->insert([
            ['nama' => 'Angkatan 2023', 'tahun_mulai' => 2023, 'tahun_selesai' => 2026, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama' => 'Angkatan 2024', 'tahun_mulai' => 2024, 'tahun_selesai' => 2027, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama' => 'Angkatan 2025', 'tahun_mulai' => 2025, 'tahun_selesai' => 2028, 'created_at'=>now(), 'updated_at'=>now()],
        ]);
    }
}

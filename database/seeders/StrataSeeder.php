<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('strata')->insert([
            // Tahfizh: harian
            ['nama'=>'Tamhidi',   'target_baris'=>5, 'frekuensi'=>'harian',   'jenis_kelas'=>'tahfizh', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Takmili',   'target_baris'=>7, 'frekuensi'=>'harian',   'jenis_kelas'=>'tahfizh', 'created_at'=>now(), 'updated_at'=>now()],
            // Reguler: mingguan
            ['nama'=>'Akselerasi','target_baris'=>7, 'frekuensi'=>'mingguan', 'jenis_kelas'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Reguler',   'target_baris'=>5, 'frekuensi'=>'mingguan', 'jenis_kelas'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Tahsin',    'target_baris'=>3, 'frekuensi'=>'mingguan', 'jenis_kelas'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
        ]);
    }
}

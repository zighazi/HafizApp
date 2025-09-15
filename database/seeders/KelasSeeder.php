<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil id angkatan supaya referensinya valid
        $a2023 = DB::table('angkatan')->where('nama','Angkatan 2023')->value('id');
        $a2024 = DB::table('angkatan')->where('nama','Angkatan 2024')->value('id');
        $a2025 = DB::table('angkatan')->where('nama','Angkatan 2025')->value('id');

        DB::table('kelas')->insert([
            ['nama'=>'X E1',   'angkatan_id'=>$a2023, 'jenis'=>'tahfizh', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'X E2',   'angkatan_id'=>$a2023, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'X E3',   'angkatan_id'=>$a2023, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'X E4',   'angkatan_id'=>$a2023, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],

            ['nama'=>'XI F1',  'angkatan_id'=>$a2024, 'jenis'=>'tahfizh', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XI F2',  'angkatan_id'=>$a2024, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XI F3',  'angkatan_id'=>$a2024, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XI F4',  'angkatan_id'=>$a2024, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],

            ['nama'=>'XII F1', 'angkatan_id'=>$a2025, 'jenis'=>'tahfizh', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XII F2', 'angkatan_id'=>$a2025, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XII F3', 'angkatan_id'=>$a2025, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'XII F4', 'angkatan_id'=>$a2025, 'jenis'=>'reguler', 'created_at'=>now(), 'updated_at'=>now()],
        ]);
    }
}

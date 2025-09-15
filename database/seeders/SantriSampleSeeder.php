<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SantriSampleSeeder extends Seeder
{
    public function run(): void
    {
        $k_XE1   = DB::table('kelas')->where('nama','X E1')->value('id');   // tahfizh
        $k_XE2   = DB::table('kelas')->where('nama','X E2')->value('id');   // reguler

        $st_tamhidi  = DB::table('strata')->where(['nama'=>'Tamhidi', 'jenis_kelas'=>'tahfizh'])->value('id');
        $st_reguler  = DB::table('strata')->where(['nama'=>'Reguler',  'jenis_kelas'=>'reguler'])->value('id');

        $a2023 = DB::table('angkatan')->where('nama','Angkatan 2023')->value('id');

        DB::table('santri')->insert([
            [
                'nis'=>'2023001','nama'=>'Ahmad Fauzi','jenis_kelamin'=>'L',
                'angkatan_id'=>$a2023,'kelas_id'=>$k_XE1,'strata_id'=>$st_tamhidi,
                'created_at'=>now(),'updated_at'=>now()
            ],
            [
                'nis'=>'2023002','nama'=>'Siti Aminah','jenis_kelamin'=>'P',
                'angkatan_id'=>$a2023,'kelas_id'=>$k_XE2,'strata_id'=>$st_reguler,
                'created_at'=>now(),'updated_at'=>now()
            ],
        ]);
    }
}

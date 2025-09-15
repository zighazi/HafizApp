<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefSeeder extends Seeder
{
    public function run(): void
    {
        // ANGKATAN (sesuai kebutuhan saat ini)
        DB::table('angkatans')->upsert([
            ['tahun'=>'2023','label'=>'Angkatan 2023'],
            ['tahun'=>'2024','label'=>'Angkatan 2024'],
            ['tahun'=>'2025','label'=>'Angkatan 2025'],
        ], ['tahun'], ['label']);

        // STRATA
        DB::table('strata')->upsert([
            ['stream'=>'TAHFIZH','nama_strata'=>'Tamhidi'],
            ['stream'=>'TAHFIZH','nama_strata'=>'Takmili'],
            ['stream'=>'REGULER','nama_strata'=>'Tahsin'],
            ['stream'=>'REGULER','nama_strata'=>'Reguler'],
            ['stream'=>'REGULER','nama_strata'=>'Akselerasi'],
        ], ['stream','nama_strata'], []);

        // KELAS
        $now = now();
        $rows = [];

        // X.E1..E4 (E1 Tahfizh khusus, lain REGULER)
        foreach (range(1,4) as $i) {
            $stream = ($i === 1) ? 'TAHFIZH' : 'REGULER';
            $rows[] = ['grade'=>'X','kode'=>"X.E{$i}", 'nama_kelas'=>"X.E{$i}", 'stream'=>$stream, 'is_special'=>($i===1), 'created_at'=>$now, 'updated_at'=>$now];
        }
        // XI.F1..F4 (F1 Tahfizh khusus)
        foreach (range(1,4) as $i) {
            $stream = ($i === 1) ? 'TAHFIZH' : 'REGULER';
            $rows[] = ['grade'=>'XI','kode'=>"XI.F{$i}", 'nama_kelas'=>"XI.F{$i}", 'stream'=>$stream, 'is_special'=>($i===1), 'created_at'=>$now, 'updated_at'=>$now];
        }
        // XII.F1..F4 (F1 Tahfizh khusus)
        foreach (range(1,4) as $i) {
            $stream = ($i === 1) ? 'TAHFIZH' : 'REGULER';
            $rows[] = ['grade'=>'XII','kode'=>"XII.F{$i}", 'nama_kelas'=>"XII.F{$i}", 'stream'=>$stream, 'is_special'=>($i===1), 'created_at'=>$now, 'updated_at'=>$now];
        }

        DB::table('kelas')->upsert($rows, ['grade','kode'], ['nama_kelas','stream','is_special','updated_at']);

        // SET PROMOTION CHAIN: X.Ei -> XI.Fi -> XII.Fi
        $kelas = DB::table('kelas')->get()->keyBy('kode');

        foreach (range(1,4) as $i) {
            $xe = $kelas["X.E{$i}"]->id ?? null;
            $xif= $kelas["XI.F{$i}"]->id ?? null;
            $xiif= $kelas["XII.F{$i}"]->id ?? null;

            if ($xe && $xif)  DB::table('kelas')->where('id',$xe)->update(['next_kelas_id'=>$xif, 'updated_at'=>$now]);
            if ($xif && $xiif) DB::table('kelas')->where('id',$xif)->update(['next_kelas_id'=>$xiif, 'updated_at'=>$now]);
            // XII.* next_kelas_id biarkan null (lulus)
        }
    }
}
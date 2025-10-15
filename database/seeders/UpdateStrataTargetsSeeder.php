<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateStrataTargetsSeeder extends Seeder
{
    public function run(): void
    {
        // Silakan sesuaikan target real kamu
        $targets = [
            'Tamhidi'   => 80,
            'Tahsin'    => 100,
            'Takmili'   => 120,
            'Akselerasi'=> 150,
            'Reguler'   => 100,
        ];

        foreach ($targets as $nama => $target) {
            DB::table('strata')
                ->where('nama', $nama)
                ->update(['target_baris_mingguan' => $target]);
        }
    }
}
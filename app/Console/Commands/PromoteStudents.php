<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Santri, Kelas, Angkatan, Enrollment};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromoteStudents extends Command
{
    protected $signature = 'hafizapp:promote {tahunAjaranBaru}'; // ex: 2025/2026
    protected $description = 'Naikkan kelas santri & buat enrollment baru untuk tahun ajaran yang dituju';

    public function handle(): int
    {
        $tahunBaru = $this->argument('tahunAjaranBaru');
        if (!preg_match('/^\d{4}\/\d{4}$/', $tahunBaru)) {
            $this->error('Format tahun ajaran harus YYYY/YYYY, mis: 2025/2026'); return 1;
        }

        // mapping angkatan by grade untuk tahun baru:
        // X -> 2026, XI -> 2025, XII -> 2024 jika ingin dinamis;
        // tapi sesuai requirement Anda saat ini:
        $angkatanX  = Angkatan::firstOrCreate(['tahun'=>'2025'], ['label'=>'Angkatan 2025']);
        $angkatanXI = Angkatan::firstOrCreate(['tahun'=>'2024'], ['label'=>'Angkatan 2024']);
        $angkatanXII= Angkatan::firstOrCreate(['tahun'=>'2023'], ['label'=>'Angkatan 2023']);

        $angkatanByGrade = [
            'X'   => $angkatanX->id,
            'XI'  => $angkatanXI->id,
            'XII' => $angkatanXII->id,
        ];

        $now = Carbon::now();

        DB::transaction(function () use ($tahunBaru, $angkatanByGrade, $now) {
            // Ambil semua santri aktif
            $santris = Santri::with('kelas')->get();

            foreach ($santris as $s) {
                $kelasSekarang = $s->kelas;
                if (!$kelasSekarang) continue;

                // tentukan kelas target
                $kelasNext = $kelasSekarang->next()->first();

                if ($kelasNext) {
                    // update santri ke kelas baru
                    $s->kelas_id = $kelasNext->id;
                    // set angkatan berdasarkan grade barunya
                    $s->angkatan_id = $angkatanByGrade[$kelasNext->grade] ?? null;
                    // validasi strata by stream (opsional: sesuaikan strata si santri)
                    if ($s->strata && $s->strata->stream !== $kelasNext->stream) {
                        // reset strata jika tidak cocok stream
                        $s->strata_id = null;
                    }
                    $s->save();
                } else {
                    // XII -> lulus
                    $s->graduated_at = $now;
                    $s->save();
                }

                // catat enrollment tahun ajaran baru
                Enrollment::updateOrCreate(
                    ['santri_id'=>$s->id, 'tahun_ajaran'=>$tahunBaru],
                    [
                        'angkatan_id'=>$s->angkatan_id,
                        'kelas_id'=>$s->kelas_id,
                        'strata_id'=>$s->strata_id,
                        'promoted_at'=>$now
                    ]
                );
            }
        });

        $this->info("Promosi selesai untuk tahun ajaran {$tahunBaru}");
        return 0;
    }
}
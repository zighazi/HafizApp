<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HafalanSampleSeeder extends Seeder
{
    public function run(): void
    {
        $surahs = DB::table('surahs')
            ->select('id','nomor','nama_id','jumlah_ayat')
            ->orderBy('nomor')
            ->get();

        if ($surahs->isEmpty()) {
            $this->command->warn('Surahs kosong. Jalankan SurahSeeder dulu.');
            return;
        }

        $santriRows = DB::table('santri as s')
            ->join('kelas as k', 'k.id', '=', 's.kelas_id')
            ->join('strata as st', 'st.id', '=', 's.strata_id')
            ->select(
                's.id as santri_id', 's.nama as santri_nama',
                'k.nama as kelas_nama', 'k.jenis as kelas_jenis',
                'st.nama as strata_nama', 'st.target_baris', 'st.frekuensi'
            )
            ->orderBy('s.id')
            ->get();

        if ($santriRows->isEmpty()) {
            $this->command->warn('Santri kosong. Isi dulu master santri.');
            return;
        }

        $now          = Carbon::now();
        $startTahfizh = $now->copy()->subDays(21)->startOfDay();
        $startReguler = $now->copy()->subMonths(2)->startOfMonth();

        $toInsert = [];

        foreach ($santriRows as $row) {
            $isTahfizh = $row->kelas_jenis === 'tahfizh';
            $target    = (int) $row->target_baris;
            $guru      = Str::of($row->kelas_nama)->upper();

            if ($isTahfizh) {
                // Harian: 14–21 hari terakhir
                $days = rand(14, 21);
                for ($i = 0; $i < $days; $i++) {
                    $tanggal = $startTahfizh->copy()->addDays($i);
                    if ($tanggal->gt($now)) break;
                    if (rand(1, 100) <= 30) continue; // 30% libur

                    $len   = max(1, $target + rand(-2, 2));
                    $surah = $surahs->random();
                    [$mulai, $selesai] = $this->pickAyatRange($surah->jumlah_ayat, $len);

                    $toInsert[] = [
                        'santri_id'     => $row->santri_id,
                        'surah_id'      => $surah->id,
                        'tanggal_setor' => $tanggal->toDateString(),
                        'ayat_mulai'    => $mulai,
                        'ayat_selesai'  => $selesai,
                        'metode'        => 'setoran',
                        'penilai_guru'  => 'Ust '.$guru,
                        'catatan'       => null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            } else {
                // Bulanan: 2 bulan terakhir, 1–3 kali per bulan
                $cursor = $startReguler->copy();
                while ($cursor->lte($now)) {
                    $pertemuan = rand(1, 3);
                    for ($p = 0; $p < $pertemuan; $p++) {
                        $tanggal = $cursor->copy()->addDays(rand(0, $cursor->daysInMonth - 1));
                        if ($tanggal->gt($now)) continue;

                        $len   = max(1, $target + rand(-2, 2));
                        $surah = $surahs->random();
                        [$mulai, $selesai] = $this->pickAyatRange($surah->jumlah_ayat, $len);

                        $toInsert[] = [
                            'santri_id'     => $row->santri_id,
                            'surah_id'      => $surah->id,
                            'tanggal_setor' => $tanggal->toDateString(),
                            'ayat_mulai'    => $mulai,
                            'ayat_selesai'  => $selesai,
                            'metode'        => 'setoran',
                            'penilai_guru'  => 'Ust '.$guru,
                            'catatan'       => null,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }
                    $cursor->addMonth()->startOfMonth();
                }
            }
        }

        foreach (array_chunk($toInsert, 500) as $chunk) {
            DB::table('hafalans')->insert($chunk);
        }

        $this->command->info('HafalanSampleSeeder selesai: '.count($toInsert).' baris.');
    }

    private function pickAyatRange(int $jumlahAyat, int $len): array
    {
        $len = max(1, $len);
        if ($len >= $jumlahAyat) {
            return [1, $jumlahAyat];
        }
        $start = rand(1, max(1, $jumlahAyat - $len + 1));
        $end   = min($jumlahAyat, $start + $len - 1);
        return [$start, $end];
    }
}
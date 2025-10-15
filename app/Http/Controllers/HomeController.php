<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // --- Total Santri ---
        $totalSantri = 0;
        try {
            $totalSantri = Santri::count();
        } catch (\Throwable $e) {
            $totalSantri = 0;
        }

        // --- Kelas aktif: pakai kelas_id kalau ada, fallback ke kolom teks 'kelas' ---
        $kelasAktif = 0;
        try {
            $santriTable = (new Santri)->getTable();      // 'santri' / 'santris'
            if (Schema::hasTable($santriTable)) {
                if (Schema::hasColumn($santriTable, 'kelas_id')) {
                    $kelasAktif = DB::table($santriTable)->whereNotNull('kelas_id')->distinct()->count('kelas_id');
                } elseif (Schema::hasColumn($santriTable, 'kelas')) {
                    $kelasAktif = DB::table($santriTable)->whereNotNull('kelas')->distinct()->count('kelas');
                }
            }
        } catch (\Throwable $e) {
            $kelasAktif = 0;
        }

        // --- Rekap 6 bulan terakhir: aman walau tabel/kolom belum ada ---
        $totalHafalan = 0;
        $rekap = collect();

        try {
            if (class_exists(\App\Models\Hafalan::class)) {
                $hf = new \App\Models\Hafalan();
                $hfTable = $hf->getTable();              // 'hafalan' / 'hafalans'
                if (Schema::hasTable($hfTable)) {
                    $dateCol = Schema::hasColumn($hfTable, 'tanggal') ? 'tanggal' : 'created_at';
                    $rekap = DB::table($hfTable)
                        ->selectRaw("DATE_FORMAT($dateCol, '%Y-%m') as bulan, COUNT(*) as total")
                        ->groupBy('bulan')->orderBy('bulan', 'desc')
                        ->limit(6)->get()->reverse();
                    $totalHafalan = DB::table($hfTable)->count();
                }
            }
        } catch (\Throwable $e) {
            $totalHafalan = 0;
            $rekap = collect();
        }

        // --- List santri terbaru untuk tabel kecil di dashboard ---
        $santris = collect();
        try {
            $santris = Santri::latest()->limit(10)->get();
        } catch (\Throwable $e) {
            $santris = collect();
        }

        return view('dashboard', compact('totalSantri', 'totalHafalan', 'kelasAktif', 'rekap', 'santris'));
    }
}
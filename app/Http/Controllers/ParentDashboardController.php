<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Hafalan;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
{
    $santris = Santri::orderBy('nama')->get(['nis','nama','strata']);

    $activeSantri = null;
    if ($request->filled('santri_id')) {
        $activeSantri = Santri::where('nis', $request->get('santri_id'))->first();
    }
    if (!$activeSantri) {
        $activeSantri = $santris->first();
    }

    if (!$activeSantri) {
        return view('dashboard.orangtua', [
            'santris' => $santris,
            'activeSantri' => null,
            'summary' => [
                'week_sets' => 0, 'week_baris' => 0,
                'month_sets' => 0, 'month_baris' => 0,
                'target_baris_mingguan' => 0, 'tuntas_mingguan' => false, 'progress_mingguan' => 0,
            ],
            'recent' => collect(),
            // NEW:
            'filters' => ['preset' => 'week', 'start' => null, 'end' => null, 'label' => 'Minggu ini'],
        ]);
    }

    // ====== RANGE FILTER ======
    // presets: week (default) | month | quarter | custom
    $preset = $request->get('preset', 'week');
    $start  = $request->date('start'); // custom
    $end    = $request->date('end');

    $now = \Carbon\Carbon::now();
    if ($preset === 'month') {
        $rangeStart = $now->copy()->startOfMonth();
        $rangeEnd   = $now->copy()->endOfMonth();
        $label = 'Bulan ini';
    } elseif ($preset === 'quarter') {
        $rangeStart = $now->copy()->subMonths(2)->startOfMonth(); // 3 bulan terakhir
        $rangeEnd   = $now->copy()->endOfMonth();
        $label = '3 Bulan terakhir';
    } elseif ($preset === 'custom' && $start && $end) {
        $rangeStart = \Carbon\Carbon::parse($start)->startOfDay();
        $rangeEnd   = \Carbon\Carbon::parse($end)->endOfDay();
        $label = $rangeStart->format('d M Y').' – '.$rangeEnd->format('d M Y');
    } else {
        // default: week
        $rangeStart = $now->copy()->startOfWeek(); // Senin
        $rangeEnd   = $now->copy()->endOfWeek();   // Minggu
        $label = 'Minggu ini';
        $preset = 'week';
    }

    // Juga butuh batas minggu & bulan untuk kartu "Minggu ini / Bulan ini"
    $startOfWeek  = $now->copy()->startOfWeek();
    $endOfWeek    = $now->copy()->endOfWeek();
    $startOfMonth = $now->copy()->startOfMonth();
    $endOfMonth   = $now->copy()->endOfMonth();

    $sumBarisExpr = "COALESCE(jumlah_baris, GREATEST(0, COALESCE(ayat_selesai,0) - COALESCE(ayat_mulai,0) + 1))";

    // === Summary minggu ini
    $week = Hafalan::where('nis', $activeSantri->nis)
        ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

    $weekSets  = (clone $week)->count();
    $weekBaris = (clone $week)->selectRaw("SUM($sumBarisExpr) as total_baris")->value('total_baris') ?? 0;

    // === Summary bulan ini
    $month = Hafalan::where('nis', $activeSantri->nis)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

    $monthSets  = (clone $month)->count();
    $monthBaris = (clone $month)->selectRaw("SUM($sumBarisExpr) as total_baris")->value('total_baris') ?? 0;

    // === Target mingguan berdasar strata
    $targetMingguan = null;
    if (!empty($activeSantri->strata)) {
        $targetMingguan = DB::table('strata')
            ->where('nama', $activeSantri->strata)
            ->value('target_baris_mingguan');
    }
    $targetMingguan = (int) ($targetMingguan ?? 100);

    $tuntasMingguan = $weekBaris >= $targetMingguan;
    $progress = $targetMingguan > 0 ? min(100, (int) round(($weekBaris / $targetMingguan) * 100)) : 0;

    $summary = [
        'week_sets'  => $weekSets,
        'week_baris' => (int) $weekBaris,
        'month_sets' => $monthSets,
        'month_baris'=> (int) $monthBaris,
        'target_baris_mingguan' => $targetMingguan,
        'tuntas_mingguan'       => $tuntasMingguan,
        'progress_mingguan'     => $progress,
    ];

    // === Riwayat sesuai FILTER (rangeStart-rangeEnd)
    $recent = Hafalan::where('nis', $activeSantri->nis)
        ->leftJoin('surahs', 'surahs.id', '=', 'hafalans.surah_id')
        ->whereBetween('hafalans.created_at', [$rangeStart, $rangeEnd])
        ->orderByDesc('hafalans.created_at')
        ->limit(200)
        ->get([
            'hafalans.id',
            'hafalans.created_at',
            'hafalans.ayat_mulai',
            'hafalans.ayat_selesai',
            DB::raw("$sumBarisExpr as baris"),
            'surahs.nama as surah_nama',
        ]);

    return view('dashboard.orangtua', [
        'santris' => $santris,
        'activeSantri' => $activeSantri,
        'summary' => $summary,
        'recent' => $recent,
        // NEW: info filter untuk view
        'filters' => [
            'preset' => $preset,
            'start'  => $preset === 'custom' ? $rangeStart->toDateString() : null,
            'end'    => $preset === 'custom' ? $rangeEnd->toDateString() : null,
            'label'  => $label,
        ],
    ]);
}


    public function apiHafalan(Santri $santri, Request $request)
    {
        $start = Carbon::now()->startOfMonth()->subMonths(5);
        $end   = Carbon::now()->endOfMonth();

        $sumBarisExpr = "COALESCE(jumlah_baris, GREATEST(0, COALESCE(ayat_selesai,0) - COALESCE(ayat_mulai,0) + 1))";

        $rows = Hafalan::where('nis', $santri->nis)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym,
                         COUNT(*) as sets,
                         SUM($sumBarisExpr) as baris")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = [];
        $sets = [];
        $baris = [];

        $cursor = $start->copy();
        $map = $rows->keyBy('ym');

        while ($cursor <= $end) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->locale('id')->translatedFormat('MMM yyyy');
            $sets[]   = (int) ($map[$ym]->sets ?? 0);
            $baris[]  = (int) ($map[$ym]->baris ?? 0);
            $cursor->addMonth();
        }

        return response()->json([
            'labels' => $labels,
            'sets'   => $sets,
            'baris'  => $baris,
        ]);
    }
}
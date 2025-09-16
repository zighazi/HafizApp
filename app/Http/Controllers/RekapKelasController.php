<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Hafalan;
use App\Models\Santri;

class RekapKelasController extends Controller
{
    /** GET /rekap/kelas -> arahkan ke bulanan */
    public function index(Request $request) { return $this->bulanan($request); }

    /** ---------------- BULANAN ---------------- */
    public function bulanan(Request $request)
    {
        [$month, $year] = $this->validatedMonthYear($request->query('bulan'), $request->query('tahun'));

        // deteksi kolom kelas: kelas, kelas_kode, kelas_nama, rombel, kelas_id
        [$kelasCol, $kelasLabel] = $this->resolveKelasColumn();

        $kelasFilter = null;
        if ($kelasCol) {
            $kelasFilter = trim((string) $request->query('kelas', '')) ?: null;
        }

        $kelasList = $kelasCol ? $this->kelasDistinct($kelasCol) : collect();

        $selects = [
            'santri.id as santri_id',
            'santri.nama as santri_nama',
            DB::raw('COUNT(hafalans.id) as jumlah_setoran'),
            DB::raw('SUM(hafalans.ayat_selesai - hafalans.ayat_mulai + 1) as total_ayat'),
        ];
        if ($kelasCol) $selects[] = "santri.$kelasCol as kelas_val";

        $rows = Hafalan::query()
            ->join('santri', 'santri.id', '=', 'hafalans.santri_id')
            ->whereYear('hafalans.tanggal_setor', $year)
            ->whereMonth('hafalans.tanggal_setor', $month)
            ->when($kelasCol && $kelasFilter, fn($q) => $q->where("santri.$kelasCol", $kelasFilter))
            ->groupBy('santri.id', 'santri.nama', ...($kelasCol ? ["santri.$kelasCol"] : []))
            ->orderBy('santri.nama')
            ->get($selects);

        $totalSetoranKelas = (int) $rows->sum('jumlah_setoran');
        $totalAyatKelas    = (int) $rows->sum('total_ayat');
        $years             = $this->yearOptions();

        return view('rekap_kelas.bulanan', [
            'rows'              => $rows,
            'month'             => $month,
            'year'              => $year,
            'years'             => $years,
            'hasKelas'          => (bool) $kelasCol,
            'kelas'             => $kelasFilter,
            'kelasList'         => $kelasList,
            'kelasLabel'        => $kelasLabel,   // <-- gunakan label ini di Blade
            'totalSetoranKelas' => $totalSetoranKelas,
            'totalAyatKelas'    => $totalAyatKelas,
        ]);
    }

    public function exportBulananCsv(Request $request)
    {
        [$month, $year] = $this->validatedMonthYear($request->query('bulan'), $request->query('tahun'));

        [$kelasCol]   = $this->resolveKelasColumn();
        $kelasFilter  = $kelasCol ? (trim((string) $request->query('kelas', '')) ?: null) : null;

        $selects = [
            'santri.nama as Nama',
            DB::raw('COUNT(hafalans.id) as Jumlah_Setoran'),
            DB::raw('SUM(hafalans.ayat_selesai - hafalans.ayat_mulai + 1) as Total_Ayat'),
        ];
        if ($kelasCol) $selects[] = "santri.$kelasCol as Kelas";

        $data = Hafalan::query()
            ->join('santri', 'santri.id', '=', 'hafalans.santri_id')
            ->whereYear('hafalans.tanggal_setor', $year)
            ->whereMonth('hafalans.tanggal_setor', $month)
            ->when($kelasCol && $kelasFilter, fn($q) => $q->where("santri.$kelasCol", $kelasFilter))
            ->groupBy('santri.id', 'santri.nama', ...($kelasCol ? ["santri.$kelasCol"] : []))
            ->orderBy('santri.nama')
            ->get($selects);

        $headers = $kelasCol ? ['Nama','Kelas','Jumlah Setoran','Total Ayat']
                             : ['Nama','Jumlah Setoran','Total Ayat'];

        $lines = [implode(',', $headers)];
        foreach ($data as $row) {
            $lines[] = $kelasCol
                ? sprintf('"%s","%s",%d,%d', $row->Nama, $row->Kelas, $row->Jumlah_Setoran, $row->Total_Ayat)
                : sprintf('"%s",%d,%d',      $row->Nama,         $row->Jumlah_Setoran, $row->Total_Ayat);
        }

        $filename = 'rekap_bulanan_'.$year.'_'.str_pad((string)$month,2,'0',STR_PAD_LEFT)
                  . ($kelasFilter ? '_kelas_'.preg_replace('/[^A-Za-z0-9_\-]/','_',$kelasFilter) : '')
                  . '.csv';

        return Response::make(implode("\n", $lines), 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /** ---------------- TAHUNAN ---------------- */
    public function tahunan(Request $request)
    {
        [, $year] = $this->validatedMonthYear(null, $request->query('tahun'));

        [$kelasCol, $kelasLabel] = $this->resolveKelasColumn();
        $kelasFilter = $kelasCol ? (trim((string) $request->query('kelas', '')) ?: null) : null;
        $kelasList   = $kelasCol ? $this->kelasDistinct($kelasCol) : collect();

        $selects = [
            'santri.id as santri_id',
            'santri.nama as santri_nama',
            DB::raw('COUNT(hafalans.id) as jumlah_setoran'),
            DB::raw('SUM(hafalans.ayat_selesai - hafalans.ayat_mulai + 1) as total_ayat'),
        ];
        if ($kelasCol) $selects[] = "santri.$kelasCol as kelas_val";

        $rows = Hafalan::query()
            ->join('santri', 'santri.id', '=', 'hafalans.santri_id')
            ->whereYear('hafalans.tanggal_setor', $year)
            ->when($kelasCol && $kelasFilter, fn($q) => $q->where("santri.$kelasCol", $kelasFilter))
            ->groupBy('santri.id', 'santri.nama', ...($kelasCol ? ["santri.$kelasCol"] : []))
            ->orderBy('santri.nama')
            ->get($selects);

        $totalSetoranKelas = (int) $rows->sum('jumlah_setoran');
        $totalAyatKelas    = (int) $rows->sum('total_ayat');
        $years             = $this->yearOptions();

        return view('rekap_kelas.tahunan', [
            'rows'              => $rows,
            'year'              => $year,
            'years'             => $years,
            'hasKelas'          => (bool) $kelasCol,
            'kelas'             => $kelasFilter,
            'kelasList'         => $kelasList,
            'kelasLabel'        => $kelasLabel,
            'totalSetoranKelas' => $totalSetoranKelas,
            'totalAyatKelas'    => $totalAyatKelas,
        ]);
    }

    public function exportTahunanCsv(Request $request)
    {
        [, $year] = $this->validatedMonthYear(null, $request->query('tahun'));

        [$kelasCol]  = $this->resolveKelasColumn();
        $kelasFilter = $kelasCol ? (trim((string) $request->query('kelas', '')) ?: null) : null;

        $selects = [
            'santri.nama as Nama',
            DB::raw('COUNT(hafalans.id) as Jumlah_Setoran'),
            DB::raw('SUM(hafalans.ayat_selesai - hafalans.ayat_mulai + 1) as Total_Ayat'),
        ];
        if ($kelasCol) $selects[] = "santri.$kelasCol as Kelas";

        $data = Hafalan::query()
            ->join('santri', 'santri.id', '=', 'hafalans.santri_id')
            ->whereYear('hafalans.tanggal_setor', $year)
            ->when($kelasCol && $kelasFilter, fn($q) => $q->where("santri.$kelasCol", $kelasFilter))
            ->groupBy('santri.id', 'santri.nama', ...($kelasCol ? ["santri.$kelasCol"] : []))
            ->orderBy('santri.nama')
            ->get($selects);

        $headers = $kelasCol ? ['Nama','Kelas','Jumlah Setoran','Total Ayat'] : ['Nama','Jumlah Setoran','Total Ayat'];

        $lines = [implode(',', $headers)];
        foreach ($data as $row) {
            $lines[] = $kelasCol
                ? sprintf('"%s","%s",%d,%d', $row->Nama, $row->Kelas, $row->Jumlah_Setoran, $row->Total_Ayat)
                : sprintf('"%s",%d,%d',      $row->Nama,         $row->Jumlah_Setoran, $row->Total_Ayat);
        }

        $filename = 'rekap_tahunan_'.$year
                  . ($kelasFilter ? '_kelas_'.preg_replace('/[^A-Za-z0-9_\-]/','_',$kelasFilter) : '')
                  . '.csv';

        return Response::make(implode("\n", $lines), 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function printTahunan(Request $request)
    {
        [, $year] = $this->validatedMonthYear(null, $request->query('tahun'));

        [$kelasCol]  = $this->resolveKelasColumn();
        $kelasFilter = $kelasCol ? (trim((string) $request->query('kelas', '')) ?: null) : null;

        $selects = [
            'santri.nama as santri_nama',
            DB::raw('COUNT(hafalans.id) as jumlah_setoran'),
            DB::raw('SUM(hafalans.ayat_selesai - hafalans.ayat_mulai + 1) as total_ayat'),
        ];
        if ($kelasCol) $selects[] = "santri.$kelasCol as kelas_val";

        $rows = Hafalan::query()
            ->join('santri', 'santri.id', '=', 'hafalans.santri_id')
            ->whereYear('hafalans.tanggal_setor', $year)
            ->when($kelasCol && $kelasFilter, fn($q) => $q->where("santri.$kelasCol", $kelasFilter))
            ->groupBy('santri.id', 'santri.nama', ...($kelasCol ? ["santri.$kelasCol"] : []))
            ->orderBy('santri.nama')
            ->get($selects);

        $totalSetoranKelas = (int) $rows->sum('jumlah_setoran');
        $totalAyatKelas    = (int) $rows->sum('total_ayat');

        return view('rekap_kelas.tahunan_print', [
            'rows'   => $rows,
            'year'   => $year,
            'hasKelas' => (bool) $kelasCol,
            'kelas'  => $kelasFilter,
            'totalSetoranKelas' => $totalSetoranKelas,
            'totalAyatKelas'    => $totalAyatKelas,
        ]);
    }

    /* ---------------- Helpers ---------------- */

    /** validasi bulan/tahun (fallback ke sekarang) */
    private function validatedMonthYear($bulan, $tahun): array
    {
        $m = (int) ($bulan ?? now()->month);
        $y = (int) ($tahun ?? now()->year);
        if ($m < 1 || $m > 12) $m = (int) now()->month;
        if ($y < 2000 || $y > 2100) $y = (int) now()->year;
        return [$m, $y];
    }

    /** daftar tahun: 5 thn ke belakang + tahun ini + 1 thn depan */
    private function yearOptions(): Collection
    {
        $start = now()->year - 5;
        $end   = now()->year + 1;
        return collect(range($end, $start))->sortDesc()->values();
    }

    /**
     * Tentukan kolom "kelas" yang tersedia di tabel santri.
     * @return array{0:?string,1:string} [namaKolom|null, labelTampil]
     */
    private function resolveKelasColumn(): array
    {
        $candidates = ['kelas', 'kelas_kode', 'kelas_nama', 'rombel', 'kelas_id'];
        foreach ($candidates as $c) {
            if (Schema::hasColumn('santri', $c)) {
                // label tampil default
                $label = match ($c) {
                    'kelas_kode' => 'Kelas',
                    'kelas_nama' => 'Kelas',
                    'rombel'     => 'Rombel',
                    'kelas_id'   => 'Kelas',
                    default      => 'Kelas',
                };
                return [$c, $label];
            }
        }
        return [null, 'Kelas'];
    }

    /** ambil daftar kelas distinct dari kolom yang dipilih */
    private function kelasDistinct(string $kelasCol): Collection
    {
        return Santri::query()
            ->whereNotNull($kelasCol)
            ->distinct()
            ->orderBy($kelasCol)
            ->pluck($kelasCol);
    }
}
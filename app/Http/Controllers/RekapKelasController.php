<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapKelasController extends Controller
{
    /** GET /rekap/kelas */
    public function index()
    {
        $rekap = [
            ['kelas' => 'X.E1',  'jumlah_santri' => 30, 'total_setoran' => 120],
            ['kelas' => 'X.E2',  'jumlah_santri' => 28, 'total_setoran' => 110],
            ['kelas' => 'XI.F1', 'jumlah_santri' => 32, 'total_setoran' => 134],
        ];

        return view('rekap_kelas.index', compact('rekap'));
    }

    /** Helper export CSV sederhana */
    protected function streamCsv(string $filename, array $header, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            foreach ($rows as $r) { fputcsv($out, $r); }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** CSV keseluruhan */
    public function export()
    {
        $header = ['Kelas', 'Jumlah Santri', 'Total Setoran'];
        $rows = [
            ['X.E1', 30, 120],
            ['X.E2', 28, 110],
            ['XI.F1', 32, 134],
        ];
        return $this->streamCsv('rekap_kelas.csv', $header, $rows);
    }

    /** CSV harian */
    public function exportHarian()
    {
        $header = ['Tanggal', 'Kelas', 'Jumlah Setoran'];
        $rows = [
            [now()->toDateString(), 'X.E1', 12],
            [now()->toDateString(), 'X.E2', 9],
        ];
        return $this->streamCsv('rekap_kelas_harian.csv', $header, $rows);
    }

    /** CSV bulanan */
    public function exportBulanan()
    {
        $header = ['Bulan', 'Kelas', 'Jumlah Setoran'];
        $rows = [
            [now()->format('Y-m'), 'X.E1', 120],
            [now()->format('Y-m'), 'X.E2', 110],
        ];
        return $this->streamCsv('rekap_kelas_bulanan.csv', $header, $rows);
    }

    /** “PDF” = halaman cetak (tanpa library) */
    public function exportPdf()
    {
        $title = 'Rekap Kelas - Keseluruhan';
        $items = [
            ['kelas' => 'X.E1',  'jumlah_santri' => 30, 'total_setoran' => 120],
            ['kelas' => 'X.E2',  'jumlah_santri' => 28, 'total_setoran' => 110],
            ['kelas' => 'XI.F1', 'jumlah_santri' => 32, 'total_setoran' => 134],
        ];
        return view('rekap_kelas.print', compact('title', 'items'));
    }

    public function exportHarianPdf()
    {
        $title = 'Rekap Kelas - Harian';
        $items = [
            ['kelas' => 'X.E1', 'label' => now()->toDateString(), 'jumlah' => 12],
            ['kelas' => 'X.E2', 'label' => now()->toDateString(), 'jumlah' => 9],
        ];
        return view('rekap_kelas.print_harian', compact('title', 'items'));
    }

    public function exportBulananPdf()
    {
        $title = 'Rekap Kelas - Bulanan';
        $items = [
            ['kelas' => 'X.E1', 'label' => now()->format('Y-m'), 'jumlah' => 120],
            ['kelas' => 'X.E2', 'label' => now()->format('Y-m'), 'jumlah' => 110],
        ];
        return view('rekap_kelas.print_bulanan', compact('title', 'items'));
    }
}
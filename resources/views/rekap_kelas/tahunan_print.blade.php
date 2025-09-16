<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rekap Kelas Tahunan - {{ $year }}{{ $kelas ? ' - '.$kelas : '' }}</title>
  <style>
    * { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    body { margin: 24px; }
    h1 { margin: 0 0 12px; font-size: 20px; }
    .meta { margin-bottom: 16px; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; font-size: 12px; }
    th { background: #f5f5f5; text-align: left; }
    tfoot th { background: #fafafa; }
    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
  <div class="no-print" style="text-align:right;margin-bottom:8px;">
    <button onclick="window.print()">Print / Save as PDF</button>
  </div>

  <h1>Rekap Kelas Tahunan</h1>
  <div class="meta">
    Tahun: <strong>{{ $year }}</strong>
    @if($kelas) &nbsp; | &nbsp; {{ $kelasLabel ?? 'Kelas' }}: <strong>{{ $kelas }}</strong> @endif
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:40px">#</th>
        <th>Nama Santri</th>
        @if($hasKelas) <th style="width:120px">{{ $kelasLabel ?? 'Kelas' }}</th> @endif
        <th style="width:160px">Jumlah Setoran</th>
        <th style="width:140px">Total Ayat</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $i => $r)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $r->santri_nama }}</td>
          @if($hasKelas) <td>{{ $r->kelas_val }}</td> @endif
          <td>{{ $r->jumlah_setoran }}</td>
          <td>{{ $r->total_ayat }}</td>
        </tr>
      @empty
        <tr><td colspan="{{ $hasKelas ? 5 : 4 }}" style="text-align:center;padding:16px;">Tidak ada data.</td></tr>
      @endforelse
    </tbody>
    @if($rows->count())
      <tfoot>
        <tr>
          <th colspan="{{ $hasKelas ? 3 : 2 }}" style="text-align:right;">Total Kelas:</th>
          <th>{{ $totalSetoranKelas }}</th>
          <th>{{ $totalAyatKelas }}</th>
        </tr>
      </tfoot>
    @endif
  </table>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Cetak Rekap' }}</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; }
    h1 { font-size: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 6px 8px; font-size: 12px; }
    th { background: #f0f0f0; }
    @media print { .noprint { display: none; } }
  </style>
</head>
<body>
  <div class="noprint" style="margin-bottom:8px">
    <button onclick="window.print()">Print</button>
  </div>
  <h1>{{ $title ?? 'Rekap' }}</h1>
  <div style="margin-bottom:8px;font-size:12px;color:#555;">Tanggal cetak: {{ now()->format('Y-m-d H:i') }}</div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Kelas</th>
        <th>Jumlah Santri</th>
        <th>Total Setoran</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $i => $r)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $r['kelas'] }}</td>
          <td>{{ $r['jumlah_santri'] }}</td>
          <td>{{ $r['total_setoran'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Rekap Kelas' }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #000; }
    h2, h3 { margin: 5px 0; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; font-size: 12px; text-align: center; }
    th { background: #f0f0f0; }
    .header { text-align: center; margin-bottom: 15px; }
    .header img { display: block; margin: 0 auto 8px; }
    .meta { margin-top: 10px; font-size: 12px; }
  </style>
</head>
<body>

  {{-- HEADER --}}
  @include('partials.header_pdf')

  {{-- JUDUL --}}
  <h3>{{ $title ?? 'Rekap Status Tuntas per Kelas' }}</h3>

  {{-- META INFO --}}
  <div class="meta">
    <p><strong>Periode:</strong> {{ $meta->periode }}</p>
    <p><strong>Kelas:</strong> {{ $meta->kelas }}</p>
    <p><strong>Jenis:</strong> {{ $meta->jenis }}</p>
    <p><strong>Tanggal Cetak:</strong> {{ $meta->tanggal }}</p>
  </div>

  {{-- TABEL DATA --}}
  <table>
    <thead>
      <tr>
        <th>Kelas</th>
        <th>Jenis</th>
        <th>Total Setoran</th>
        <th>Tuntas</th>
        <th>% Tuntas</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          <td>{{ $r->kelas_nama }}</td>
          <td>{{ ucfirst($r->kelas_jenis) }}</td>
          <td>{{ $r->total_setoran }}</td>
          <td>{{ $r->total_tuntas }}</td>
          <td>{{ number_format($r->rate_tuntas, 2) }}%</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p style="margin-top:20px; font-size:11px; text-align:right;">
    Dicetak oleh sistem HafizApp pada {{ now()->format('d-m-Y H:i') }}
  </p>

</body>
</html>
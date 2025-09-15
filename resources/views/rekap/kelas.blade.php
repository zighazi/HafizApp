@extends('layouts.app')

@section('title','Rekap Status Tuntas per Kelas')

@section('content')
  <div class="card">
    <h2 style="margin-top:0">Rekap Status Tuntas per Kelas</h2>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('rekap.kelas') }}" style="margin: 10px 0 16px">
      <div class="row">
        <div>
          <label>Kelas</label>
          <select name="kelas_nama">
            <option value="">— Semua —</option>
            @foreach($kelasList as $k)
              <option value="{{ $k }}" @selected(($kelas_nama ?? '')==$k)>{{ $k }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Jenis Kelas</label>
          <select name="kelas_jenis">
            <option value="">— Semua —</option>
            <option value="tahfizh" @selected(($kelas_jenis ?? '')==='tahfizh')>Tahfizh</option>
            <option value="reguler" @selected(($kelas_jenis ?? '')==='reguler')>Reguler</option>
          </select>
        </div>

        <div>
          <label>Angkatan</label>
          <select name="angkatan_id">
            <option value="">— Semua —</option>
            @foreach($angkatanList as $a)
              <option value="{{ $a->id }}" @selected(($angkatan_id ?? '')==$a->id)>{{ $a->nama }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Strata</label>
          <select name="strata_id">
            <option value="">— Semua —</option>
            @foreach($strataList as $s)
              <option value="{{ $s->id }}" @selected(($strata_id ?? '')==$s->id)>{{ $s->nama }} ({{ $s->jenis_kelas }})</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Rentang Tanggal</label>
          <div class="row" style="grid-template-columns:1fr 1fr">
            <input type="date" name="dari" value="{{ $dari }}">
            <input type="date" name="sampai" value="{{ $sampai }}">
          </div>
        </div>
      </div>

      <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap">
  <button class="btn" type="submit">Terapkan</button>
  <a class="btn secondary" href="{{ route('rekap.kelas') }}">Reset</a>

  {{-- EXPORT BUTTONS --}}
  <a class="btn"
     href="{{ route('rekap.kelas.export', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Ringkasan (CSV)
  </a>

  <a class="btn"
     href="{{ route('rekap.kelas.export.pdf', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Ringkasan (PDF)
  </a>

  <a class="btn"
     href="{{ route('rekap.kelas.export.harian', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Harian Tahfizh (CSV)
  </a>

  <a class="btn"
     href="{{ route('rekap.kelas.export.harian.pdf', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Harian Tahfizh (PDF)
  </a>

  <a class="btn"
     href="{{ route('rekap.kelas.export.bulanan', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Bulanan Reguler (CSV)
  </a>

  <a class="btn"
     href="{{ route('rekap.kelas.export.bulanan.pdf', [
        'kelas_nama'=>$kelas_nama,'kelas_jenis'=>$kelas_jenis,
        'angkatan_id'=>$angkatan_id,'strata_id'=>$strata_id,
        'dari'=>$dari,'sampai'=>$sampai
     ]) }}">
    Export Bulanan Reguler (PDF)
  </a>
</div>
</form>

{{-- GRAFIK % TUNTAS PER KELAS --}}
<div class="card" style="margin: 10px 0 16px">
  <h3 style="margin-top:0">Grafik % Tuntas per Kelas ({{ $dari }} s.d. {{ $sampai }})</h3>
  <canvas id="chartKelas" height="120"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    const rows = @json($rekap); // [{kelas_nama, kelas_jenis, rate_tuntas}, ...]
    const labels = rows.map(r => `${r.kelas_nama} (${r.kelas_jenis})`);
    const data   = rows.map(r => r.rate_tuntas);

    const ctx = document.getElementById('chartKelas').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: '% Tuntas',
          data
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
        }
      }
    });
  })();
</script>
@endpush

    {{-- 1) RINGKAS PER KELAS --}}
    <h3 style="margin: 10px 0">Ringkasan per Kelas ({{ $dari }} s.d. {{ $sampai }})</h3>
    <div style="overflow:auto;">
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
          @forelse($rekap as $r)
            <tr>
              <td>{{ $r->kelas_nama }}</td>
              <td>{{ ucfirst($r->kelas_jenis) }}</td>
              <td>{{ $r->total_setoran }}</td>
              <td>{{ $r->total_tuntas }}</td>
              <td>{{ number_format($r->rate_tuntas,2) }}%</td>
            </tr>
          @empty
            <tr><td colspan="5">Belum ada data pada rentang/filter ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- 2) HARIAN - KHUSUS TAHFIZH --}}
    <h3 style="margin: 18px 0 8px">Detail Harian (Khusus Tahfizh)</h3>
    <div style="overflow:auto;">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Kelas</th>
            <th>Total Setoran</th>
            <th>Tuntas</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
          @forelse($harian as $h)
            @php
              $rate = $h->total_setoran>0 ? round(($h->total_tuntas*100.0)/$h->total_setoran,2) : 0.0;
            @endphp
            <tr>
              <td>{{ $h->tanggal_setor }}</td>
              <td>{{ $h->kelas_nama }}</td>
              <td>{{ $h->total_setoran }}</td>
              <td>{{ $h->total_tuntas }}</td>
              <td>{{ number_format($rate,2) }}%</td>
            </tr>
          @empty
            <tr><td colspan="5">—</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- 3) BULANAN - KHUSUS REGULER --}}
    <h3 style="margin: 18px 0 8px">Rekap Bulanan (Khusus Reguler)</h3>
    <div style="overflow:auto;">
      <table>
        <thead>
          <tr>
            <th>Bulan</th>
            <th>Rentang</th>
            <th>Kelas</th>
            <th>Total Setoran</th>
            <th>Tuntas</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bulanan as $b)
            @php
              $rate = $b->total_setoran>0 ? round(($b->total_tuntas*100.0)/$b->total_setoran,2) : 0.0;
            @endphp
            <tr>
              <td>{{ $b->label_bulan }}</td>
              <td>{{ $b->bulan_mulai }} s.d. {{ $b->bulan_selesai }}</td>
              <td>{{ $b->kelas_nama }}</td>
              <td>{{ $b->total_setoran }}</td>
              <td>{{ $b->total_tuntas }}</td>
              <td>{{ number_format($rate,2) }}%</td>
            </tr>
          @empty
            <tr><td colspan="6">—</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
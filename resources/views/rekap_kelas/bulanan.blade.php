@extends('layouts.app')
@section('title','Rekap Kelas Bulanan')

@section('content')
<div class="card p-3">
  <div class="page-head">
    <h1 class="h5 mb-0">Rekap Kelas Bulanan</h1>
    <a href="{{ route('hafalans.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Hafalan
    </a>
  </div>

  <form class="row g-2 my-3" method="GET" action="{{ route('rekap.kelas.bulanan') }}">
    <div class="col-md-3">
      <label class="form-label mb-1">Bulan</label>
      <select name="bulan" class="form-select">
        @foreach (range(1,12) as $m)
          <option value="{{ $m }}" @selected($month==$m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label mb-1">Tahun</label>
      <select name="tahun" class="form-select">
        @foreach ($years as $y)
          <option @selected($year==$y)>{{ $y }}</option>
        @endforeach
      </select>
    </div>

    @if($hasKelas)
      <div class="col-md-3">
        <label class="form-label mb-1">{{ $kelasLabel ?? 'Kelas' }}</label>
        <select name="kelas" class="form-select">
          <option value="">— Semua {{ $kelasLabel ?? 'Kelas' }} —</option>
          @foreach ($kelasList as $k)
            <option value="{{ $k }}" @selected(($kelas ?? '')==$k)>{{ $k }}</option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="col-md-4 d-flex align-items-end gap-2">
      <button class="btn btn-secondary"><i class="bi bi-filter me-1"></i> Terapkan</button>
      <a href="{{ route('rekap.kelas.bulanan.export', request()->query()) }}" class="btn btn-outline-primary">
        <i class="bi bi-filetype-csv me-1"></i> Export CSV
      </a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nama Santri</th>
          @if($hasKelas) <th>{{ $kelasLabel ?? 'Kelas' }}</th> @endif
          <th>Jumlah Setoran</th>
          <th>Total Ayat</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rows as $i => $r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->santri_nama }}</td>
            @if($hasKelas) <td>{{ $r->kelas_val ?? ($r->kelas ?? '-') }}</td> @endif
            <td>{{ $r->jumlah_setoran }}</td>
            <td>{{ $r->total_ayat }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Tidak ada data pada periode ini.</td></tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr class="table-light">
          <th colspan="{{ $hasKelas ? 3 : 2 }}" class="text-end">Total Kelas:</th>
          <th>{{ $totalSetoranKelas }}</th>
          <th>{{ $totalAyatKelas }}</th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
@extends('layouts.app')
@section('title','Rekap Kelas Tahunan')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Rekap Kelas Tahunan</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('rekap.kelas.bulanan') }}" class="btn btn-outline-secondary">Lihat Bulanan</a>
      <a href="{{ route('hafalans.create') }}" class="btn btn-primary">Tambah Hafalan</a>
    </div>
  </div>

  <form method="GET" action="{{ route('rekap.kelas.tahunan') }}" class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label mb-1">Tahun</label>
          <select name="tahun" class="form-select">
            @foreach ($years as $y)
              <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
          </select>
        </div>

        @if($hasKelas)
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $kelasLabel ?? 'Kelas' }}</label>
            <select name="kelas" class="form-select">
              <option value="">— Semua {{ $kelasLabel ?? 'Kelas' }} —</option>
              @foreach ($kelasList as $k)
                <option value="{{ $k }}" {{ ($kelas ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="col-md-4 d-grid">
          <button class="btn btn-primary">Terapkan</button>
        </div>
      </div>

      <div class="mt-3 d-flex gap-2">
        <a class="btn btn-sm btn-outline-secondary"
           href="{{ route('rekap.kelas.tahunan.export', ['tahun'=>$year] + ($kelas ? ['kelas'=>$kelas] : [])) }}">
          Export CSV
        </a>
        <a class="btn btn-sm btn-outline-secondary"
           target="_blank"
           href="{{ route('rekap.kelas.tahunan.print', ['tahun'=>$year] + ($kelas ? ['kelas'=>$kelas] : [])) }}">
          Halaman Cetak (PDF)
        </a>
      </div>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:56px">#</th>
              <th>Nama Santri</th>
              @if($hasKelas) <th>{{ $kelasLabel ?? 'Kelas' }}</th> @endif
              <th>Jumlah Setoran ({{ $year }})</th>
              <th>Total Ayat</th>
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
              <tr>
                <td colspan="{{ $hasKelas ? 5 : 4 }}" class="text-center py-4">Tidak ada data.</td>
              </tr>
            @endforelse
          </tbody>
          @if($rows->count())
            <tfoot class="table-light">
              <tr>
                <th colspan="{{ $hasKelas ? 3 : 2 }}" class="text-end">Total Kelas:</th>
                <th>{{ $totalSetoranKelas }}</th>
                <th>{{ $totalAyatKelas }}</th>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
@endsection
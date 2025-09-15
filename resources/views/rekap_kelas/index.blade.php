@extends('layouts.app')
@section('title','Rekap Kelas')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Rekap Kelas</h1>
    <div class="btn-group">
      <a href="{{ route('rekap.kelas.export') }}" class="btn btn-outline-primary btn-sm">Export CSV</a>
      <a href="{{ route('rekap.kelas.export.harian') }}" class="btn btn-outline-secondary btn-sm">Harian CSV</a>
      <a href="{{ route('rekap.kelas.export.bulanan') }}" class="btn btn-outline-secondary btn-sm">Bulanan CSV</a>
      <a href="{{ route('rekap.kelas.export.pdf') }}" class="btn btn-outline-danger btn-sm">Cetak</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Kelas</th>
              <th>Jumlah Santri</th>
              <th>Total Setoran</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($rekap as $i => $r)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $r['kelas'] }}</td>
                <td>{{ $r['jumlah_santri'] }}</td>
                <td>{{ $r['total_setoran'] }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
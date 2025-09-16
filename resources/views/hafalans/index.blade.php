@extends('layouts.app')
@section('title','Daftar Hafalan')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Daftar Hafalan</h1>
    <a href="{{ route('hafalans.create') }}" class="btn btn-primary">Tambah Hafalan</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:56px">#</th>
              <th>Nama Santri</th>
              <th>Surah</th>
              <th>Ayat</th>
              <th style="width:160px">Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($hafalans as $i => $h)
              <tr>
                <td>{{ method_exists($hafalans,'firstItem') ? $hafalans->firstItem() + $loop->index : $i+1 }}</td>
                <td>{{ $h->santri->nama ?? '-' }}</td>
                <td>{{ $h->surah->nama_id ?? '-' }}</td>
                <td>{{ $h->ayat_awal }}–{{ $h->ayat_akhir }}</td>
                <td>{{ $h->tanggal?->toDateString() ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-4">Belum ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if(method_exists($hafalans, 'links'))
    <div class="mt-3">
      {{ $hafalans->links() }}
    </div>
  @endif
@endsection
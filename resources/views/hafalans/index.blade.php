@extends('layouts.app')
@section('title','Daftar Hafalan')

@section('content')
<div class="card p-3">
  <div class="page-head">
    <h1 class="h5 mb-0">Daftar Hafalan</h1>
    <a href="{{ route('hafalans.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Hafalan
    </a>
  </div>

  <form class="row g-2 my-3" method="GET">
    <div class="col-md-6">
      <input type="text" name="q" class="form-control" placeholder="Cari nama santri..." value="{{ request('q') }}">
    </div>
    @if(($hasKelas ?? false) && isset($kelasList))
      <div class="col-md-3">
        <select name="kelas" class="form-select">
          <option value="">Semua Kelas</option>
          @foreach($kelasList as $k)
            <option value="{{ $k }}" @selected(request('kelas')==$k)>{{ $k }}</option>
          @endforeach
        </select>
      </div>
    @endif
    @if(($hasAngkatan ?? false) && isset($angkatanList))
      <div class="col-md-3">
        <select name="angkatan" class="form-select">
          <option value="">Semua Angkatan</option>
          @foreach($angkatanList as $a)
            <option @selected(request('angkatan')==$a)>{{ $a }}</option>
          @endforeach
        </select>
      </div>
    @endif
    <div class="col-md-12">
      <button class="btn btn-secondary"><i class="bi bi-filter me-1"></i> Terapkan</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nama Santri</th>
          <th>Surah</th>
          <th>Ayat</th>
          <th>Tanggal Setor</th>
        </tr>
      </thead>
      <tbody>
        @forelse($hafalans as $i => $h)
          <tr>
            <td>{{ $hafalans->firstItem() + $i }}</td>
            <td>{{ $h->santri->nama ?? '-' }}</td>
            <td>{{ $h->surah->nama_id ?? '-' }}</td>
            <td>{{ $h->ayat_mulai }}–{{ $h->ayat_selesai }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($h->tanggal_setor)->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if(method_exists($hafalans, 'links'))
    <div class="mt-2">{{ $hafalans->withQueryString()->links() }}</div>
  @endif
</div>
@endsection
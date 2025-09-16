@extends('layouts.app')
@section('title','Daftar Hafalan')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Daftar Hafalan</h1>
    <a href="{{ route('hafalans.create') }}" class="btn btn-primary">Tambah Hafalan</a>
  </div>

  {{-- FILTER BAR: hanya tampilkan kontrol yang kolomnya ada --}}
  <form method="GET" action="{{ route('hafalans.index') }}" class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        @if($hasKelas)
          <div class="col-md-3">
            <label class="form-label mb-1">Kelas</label>
            <select name="kelas" class="form-select">
              <option value="">— Semua Kelas —</option>
              @foreach ($kelasList as $k)
                <option value="{{ $k }}" {{ ($kelas ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
              @endforeach
            </select>
          </div>
        @endif

        @if($hasAngkatan)
          <div class="col-md-3">
            <label class="form-label mb-1">Angkatan</label>
            <select name="angkatan" class="form-select">
              <option value="">— Semua Angkatan —</option>
              @foreach ($angkatanList as $a)
                <option value="{{ $a }}" {{ ($angkatan ?? '') == $a ? 'selected' : '' }}>{{ $a }}</option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="col-md-{{ ($hasKelas || $hasAngkatan) ? '4' : '8' }}">
          <label class="form-label mb-1">Cari Nama Santri</label>
          <input type="text" name="q" class="form-control" value="{{ $keyword ?? '' }}" placeholder="mis. Ahmad">
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-outline-primary">Terapkan</button>
        </div>
      </div>

      @if ($kelas || $angkatan || $keyword)
        <div class="mt-2"><a href="{{ route('hafalans.index') }}" class="small">Reset filter</a></div>
      @endif
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
              @if($hasKelas)   <th>Kelas</th> @endif
              @if($hasAngkatan)<th>Angkatan</th> @endif
              <th>Surah</th>
              <th>Ayat</th>
              <th style="width:160px">Tanggal Setor</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($hafalans as $i => $h)
              <tr>
                <td>{{ method_exists($hafalans,'firstItem') ? $hafalans->firstItem() + $loop->index : $i+1 }}</td>
                <td>{{ $h->santri->nama ?? '-' }}</td>
                @if($hasKelas)    <td>{{ $h->santri->kelas ?? '-' }}</td> @endif
                @if($hasAngkatan) <td>{{ $h->santri->angkatan ?? '-' }}</td> @endif
                <td>{{ $h->surah->nama_id ?? '-' }}</td>
                <td>{{ $h->ayat_mulai }}–{{ $h->ayat_selesai }}</td>
                <td>{{ $h->tanggal_setor?->toDateString() ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="{{ 5 + ($hasKelas?1:0) + ($hasAngkatan?1:0) }}" class="text-center py-4">Belum ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if(method_exists($hafalans, 'links'))
    <div class="mt-3">{{ $hafalans->links() }}</div>
  @endif
@endsection
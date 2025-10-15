@extends('layouts.app')
@section('title','Beranda')

@section('content')
<div class="card p-4">
  <div class="page-head">
    <div>
      <h1 class="h4 mb-1">Selamat datang di HafizApp</h1>
      <p class="text-muted mb-0">Kelola semua data setoran murid-murid antum semuanya.</p>
    </div>
    @guest <a href="{{ route('login') }}" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk</a> @endguest
  </div>

  <hr class="my-4">
  <div class="row g-3">
    @php
      $cards = [
        ['icon'=>'journal-text','title'=>'Catat Hafalan','desc'=>'Tambah setoran cepat & validasi ayat otomatis.','route'=>route('hafalans.create')],
        ['icon'=>'clipboard-data','title'=>'Rekap Bulanan','desc'=>'Filter kelas & export CSV instan.','route'=>route('rekap.kelas.bulanan')],
        ['icon'=>'cloud-arrow-up','title'=>'Import Santri','desc'=>'Sinkron dari Google Sheet/CSV.','route'=>route('santris.import.form')],
      ];
    @endphp

    @foreach($cards as $c)
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="d-flex align-items-center gap-3">
          <span class="btn btn-soft btn-lg"><i class="bi bi-{{ $c['icon'] }}"></i></span>
          <div>
            <div class="fw-semibold">{{ $c['title'] }}</div>
            <div class="text-muted small">{{ $c['desc'] }}</div>
          </div>
        </div>
        @auth <a class="stretched-link" href="{{ $c['route'] }}"></a> @endauth
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
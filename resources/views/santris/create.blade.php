@extends('layouts.app')

@section('title','Tambah Santri')

@section('content')
<div class="card">
  <h2 style="margin-top:0">Tambah Santri</h2>

  <form method="POST" action="{{ route('santris.store') }}">
    @csrf
    @include('santris._form', [
      'santri'       => null,
      'angkatanList' => $angkatanList,
      'kelasList'    => $kelasList,
      'strataList'   => $strataList,
    ])

    <div style="margin-top:16px">
      <button type="submit" class="btn">Simpan</button>
      <a href="{{ route('santris.import.form') }}" class="btn secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
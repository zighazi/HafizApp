@extends('layouts.app')

@section('title','Edit Santri')

@section('content')
<div class="card">
  <h2 style="margin-top:0">Edit Santri</h2>

  <form method="POST" action="{{ route('santris.update', $santri->id) }}">
    @csrf
    @method('PUT')
    @include('santris._form', [
      'santri'       => $santri,
      'angkatanList' => $angkatanList,
      'kelasList'    => $kelasList,
      'strataList'   => $strataList,
    ])

    <div style="margin-top:16px">
      <button type="submit" class="btn">Update</button>
      <a href="{{ route('santris.import.form') }}" class="btn secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
@extends('layouts.app')

@section('title','Edit Hafalan')

@section('content')
<div class="card">
  <h2 style="margin-top:0">Edit Hafalan</h2>

  <form method="POST" action="{{ route('hafalans.update', $hafalan->id) }}">
    @csrf
    @method('PUT')

    @include('hafalans._form', [
      'hafalan' => $hafalan,
      'santris' => $santris,
      'surahs'  => $surahs,
    ])

    <div style="margin-top:16px">
      <button type="submit" class="btn">Update</button>
      <a href="{{ route('hafalans.index') }}" class="btn secondary">Batal</a>
    </div>
  </form>
</div>
@endsection
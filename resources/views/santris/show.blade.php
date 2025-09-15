@extends('layouts.app')

@section('title','Detail Santri')

@section('content')
<div class="card">
  <h2 style="margin-top:0">Detail Santri</h2>

  <table>
    <tr><th style="width:180px">NIS</th><td>{{ $santri->nis }}</td></tr>
    <tr><th>Nama</th><td>{{ $santri->nama }}</td></tr>
    <tr><th>Jenis Kelamin</th><td>{{ $santri->jenis_kelamin }}</td></tr>
    <tr><th>Angkatan</th><td>{{ $santri->angkatan->nama ?? '-' }}</td></tr>
    <tr><th>Kelas</th><td>{{ $santri->kelas->nama ?? '-' }}</td></tr>
    <tr><th>Strata</th><td>{{ $santri->strata->nama ?? '-' }}</td></tr>
  </table>

  <div style="margin-top:16px; display:flex; gap:10px">
    <a href="{{ route('santris.edit',$santri->id) }}" class="btn">Edit</a>
    <form method="POST" action="{{ route('santris.destroy',$santri->id) }}" style="display:inline" onsubmit="return confirm('Hapus santri ini?')">
      @csrf
      @method('DELETE')
      <button class="btn secondary">Hapus</button>
    </form>
    <a href="{{ route('santris.import.form') }}" class="btn secondary">Kembali</a>
  </div>
</div>
@endsection
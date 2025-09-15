@extends('layouts.app')

@section('title','Detail Hafalan')

@section('content')
<div class="card">
  <h2 style="margin-top:0">Detail Hafalan</h2>

  <table>
    <tr>
      <th style="width:180px">Tanggal Setor</th>
      <td>{{ $hafalan->tanggal_setor }}</td>
    </tr>
    <tr>
      <th>Santri</th>
      <td>{{ $hafalan->santri->nama ?? '-' }} ({{ $hafalan->santri->nis ?? '-' }})</td>
    </tr>
    <tr>
      <th>Surah</th>
      <td>
        @if($hafalan->surah)
          {{ $hafalan->surah->nomor }}. {{ $hafalan->surah->nama_id }}
        @else
          -
        @endif
      </td>
    </tr>
    <tr>
      <th>Ayat</th>
      <td>
        @php
          $mulai   = $hafalan->ayat_mulai ?? null;
          $selesai = $hafalan->ayat_selesai ?? null;
        @endphp
        {{ ($mulai && $selesai) ? ($mulai.' - '.$selesai) : '-' }}
      </td>
    </tr>
    <tr>
      <th>Status</th>
      <td>{{ $hafalan->status ? ucfirst($hafalan->status) : '-' }}</td>
    </tr>
    <tr>
      <th>Catatan</th>
      <td>{{ $hafalan->catatan ?? '—' }}</td>
    </tr>
    <tr>
      <th>Dibuat</th>
      <td>{{ $hafalan->created_at }}</td>
    </tr>
    <tr>
      <th>Diperbarui</th>
      <td>{{ $hafalan->updated_at }}</td>
    </tr>
  </table>

  <div style="margin-top:16px; display:flex; gap:10px">
    <a href="{{ route('hafalans.edit', $hafalan->id) }}" class="btn">Edit</a>
    <form method="POST" action="{{ route('hafalans.destroy', $hafalan->id) }}" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
      @csrf
      @method('DELETE')
      <button class="btn secondary">Hapus</button>
    </form>
    <a href="{{ route('hafalans.index') }}" class="btn secondary">Kembali</a>
  </div>
</div>
@endsection
@extends('layouts.app')

@section('title','Data Santri')

@section('content')
<div class="card">
  <div class="toolbar">
    <h2 style="margin:0">Data Santri</h2>
    <div class="actions">
      <a href="{{ route('santris.create') }}" class="btn">Tambah Santri</a>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <form method="GET" action="{{ route('santris.import.form') }}" style="margin-bottom:12px">
    <div class="row">
      <div>
        <label>Angkatan</label>
        <select name="angkatan_id">
          <option value="">— Semua —</option>
          @foreach($angkatanList as $a)
            <option value="{{ $a->id }}" @selected(request('angkatan_id')==$a->id)>{{ $a->nama }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Kelas</label>
        <select name="kelas_id">
          <option value="">— Semua —</option>
          @foreach($kelasList as $k)
            <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Cari (Nama/NIS)</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="ketik kata kunci...">
      </div>
    </div>
    <div style="margin-top:10px; display:flex; gap:10px">
      <button class="btn" type="submit">Terapkan</button>
      <a class="btn secondary" href="{{ route('santris.import.form') }}">Reset</a>
    </div>
  </form>

  {{-- TABEL --}}
  <div style="overflow:auto">
    <table>
      <thead>
        <tr>
          <th>NIS</th>
          <th>Nama</th>
          <th>JK</th>
          <th>Angkatan</th>
          <th>Kelas</th>
          <th>Strata</th>
          <th style="width:180px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($santris as $s)
          <tr>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->jenis_kelamin }}</td>
            <td>{{ $s->angkatan->nama ?? '-' }}</td>
            <td>{{ $s->kelas->nama ?? '-' }}</td>
            <td>{{ $s->strata->nama ?? '-' }}</td>
            <td class="actions">
              <a class="btn" href="{{ route('santris.show',$s->id) }}">Lihat</a>
              <a class="btn" href="{{ route('santris.edit',$s->id) }}">Edit</a>
              <form method="POST" action="{{ route('santris.destroy',$s->id) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn secondary" onclick="return confirm('Hapus santri ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px">
    {{ $santris->withQueryString()->links() }}
  </div>
</div>
@endsection
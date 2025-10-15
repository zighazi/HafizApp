@extends('layouts.app')
@section('title','Tambah Hafalan')

@section('content')
<form class="card p-4" method="POST" action="{{ route('hafalans.store') }}">
  @csrf

  <div class="page-head mb-3">
    <h1 class="h5 mb-0">Tambah Hafalan</h1>
    <a href="{{ route('hafalans.index') }}" class="btn btn-outline-secondary">Batal</a>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nama Santri</label>
      <select name="santri_id" class="form-select" required>
        <option value="">-- Pilih Santri --</option>
        @foreach($santris as $s)
          <option value="{{ $s->id }}" @selected(old('santri_id')==$s->id)>{{ $s->nama }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Surah</label>
      <select name="surah_id" id="surah_id" class="form-select" required>
        <option value="">-- Pilih Surah --</option>
        @foreach($surahs as $s)
          <option value="{{ $s->id }}" data-ayat="{{ $s->jumlah_ayat }}" @selected(old('surah_id')==$s->id)>
            {{ $s->nomor }}. {{ $s->nama_id }} ({{ $s->jumlah_ayat }} ayat)
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Ayat Awal</label>
      <input type="number" min="1" name="ayat_mulai" id="ayat_mulai" class="form-control"
             value="{{ old('ayat_mulai') }}" placeholder="contoh: 1" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Ayat Akhir</label>
      <input type="number" min="1" name="ayat_selesai" id="ayat_selesai" class="form-control"
             value="{{ old('ayat_selesai') }}" placeholder="contoh: 7" required>
      <div class="form-text" id="hintAyat">Pilih surah dahulu untuk melihat batas maksimal ayat.</div>
    </div>

    <div class="col-md-3">
      <label class="form-label">Tanggal Setor</label>
      <input type="date" name="tanggal_setor" class="form-control"
             value="{{ old('tanggal_setor', now()->toDateString()) }}" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Metode (opsional)</label>
      <input type="text" name="metode" class="form-control" value="{{ old('metode') }}">
    </div>

    <div class="col-md-6">
      <label class="form-label">Penilai (opsional)</label>
      <input type="text" name="penilai_guru" class="form-control" value="{{ old('penilai_guru') }}">
    </div>

    <div class="col-md-6">
      <label class="form-label">Catatan (opsional)</label>
      <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}">
    </div>
  </div>

  <div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
  </div>
</form>

@push('scripts')
<script>
  const surah = document.getElementById('surah_id');
  const mulai = document.getElementById('ayat_mulai');
  const selesai = document.getElementById('ayat_selesai');
  const hint = document.getElementById('hintAyat');

  function syncMax(){
    const opt = surah.options[surah.selectedIndex];
    const max = Number(opt?.dataset?.ayat || 0);
    if(max>0){
      mulai.max = max; selesai.max = max;
      hint.textContent = `Maksimal ayat untuk surah ini: ${max}.`;
    }else{
      mulai.removeAttribute('max'); selesai.removeAttribute('max');
      hint.textContent = 'Pilih surah dahulu untuk melihat batas maksimal ayat.';
    }
  }
  surah.addEventListener('change', syncMax);
  document.addEventListener('DOMContentLoaded', syncMax);
</script>
@endpush
@endsection
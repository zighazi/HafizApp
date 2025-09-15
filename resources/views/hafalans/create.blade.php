@extends('layouts.app')
@section('title','Tambah Hafalan')

@section('content')
  <h1 class="h4 mb-3">Tambah Hafalan</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('hafalans.store') }}">
        @csrf

        {{-- Nama Santri --}}
        <div class="mb-3">
          <label class="form-label">Nama Santri</label>
          <select name="santri_id" class="form-select" required>
            <option value="" disabled selected>-- Pilih Santri --</option>
            @foreach ($santris as $s)
              <option value="{{ $s->id }}" {{ old('santri_id') == $s->id ? 'selected' : '' }}>
                {{ $s->nama }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Surah --}}
        <div class="mb-3">
          <label class="form-label">Surah</label>
          <select name="surah_id" id="surahSelect" class="form-select" required>
            <option value="" disabled selected>-- Pilih Surah --</option>
            @foreach ($surahs as $sr)
              <option
                value="{{ $sr->id }}"
                data-max="{{ $sr->jumlah_ayat }}"
                data-nama="{{ $sr->nama }}"
                {{ old('surah_id') == $sr->id ? 'selected' : '' }}
              >
                {{ $sr->nomor }}. {{ $sr->nama }} ({{ $sr->jumlah_ayat }} ayat)
              </option>
            @endforeach
          </select>
        </div>

        {{-- Rentang Ayat: awal & akhir --}}
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Ayat Awal</label>
            <input type="number" name="ayat_awal" id="ayatAwal" class="form-control"
                   value="{{ old('ayat_awal') }}" min="1" required placeholder="contoh: 1">
          </div>
          <div class="col-md-6">
            <label class="form-label">Ayat Akhir</label>
            <input type="number" name="ayat_akhir" id="ayatAkhir" class="form-control"
                   value="{{ old('ayat_akhir') }}" min="1" required placeholder="contoh: 7">
          </div>
        </div>
        <div class="form-text mb-3" id="ayatHint">
          Pilih surah terlebih dahulu untuk melihat batas maksimal ayat.
        </div>

        {{-- Tanggal Setor --}}
        <div class="mb-3">
          <label class="form-label">Tanggal Setor</label>
          <input type="date" name="tanggal" class="form-control"
                 value="{{ old('tanggal', now()->toDateString()) }}" required>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('hafalans.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(function () {
  const surahSelect = document.getElementById('surahSelect');
  const awal        = document.getElementById('ayatAwal');
  const akhir       = document.getElementById('ayatAkhir');
  const hint        = document.getElementById('ayatHint');

  function clamp(value, min, max) {
    if (value === '' || value === null) return '';
    const v = parseInt(value, 10);
    if (isNaN(v)) return '';
    return Math.max(min, Math.min(max, v));
  }

  function syncConstraints() {
    const opt = surahSelect.options[surahSelect.selectedIndex];
    if (!opt || !opt.dataset.max) {
      awal.removeAttribute('max');
      akhir.removeAttribute('max');
      hint.textContent = 'Pilih surah terlebih dahulu untuk melihat batas maksimal ayat.';
      return;
    }
    const max  = parseInt(opt.dataset.max, 10);
    const nama = opt.dataset.nama;

    awal.setAttribute('max', max);
    akhir.setAttribute('max', max);
    awal.setAttribute('min', 1);
    akhir.setAttribute('min', 1);
    hint.textContent = `Maksimal ayat untuk ${nama}: 1 s.d. ${max}.`;

    // rapikan nilai jika melebihi batas
    if (awal.value) awal.value  = clamp(awal.value, 1, max);
    if (akhir.value) akhir.value = clamp(akhir.value, 1, max);

    // otomatis jaga awal ≤ akhir
    if (awal.value && akhir.value && parseInt(awal.value,10) > parseInt(akhir.value,10)) {
      akhir.value = awal.value;
    }
  }

  function ensureOrder() {
    if (!awal.value || !akhir.value) return;
    const a = parseInt(awal.value,10);
    const b = parseInt(akhir.value,10);
    if (!isNaN(a) && !isNaN(b) && a > b) akhir.value = a;
  }

  surahSelect.addEventListener('change', syncConstraints);
  awal.addEventListener('input', ensureOrder);
  akhir.addEventListener('input', ensureOrder);

  // init (berguna saat reload karena validation error)
  syncConstraints();
})();
</script>
@endpush
{{-- Shared form (dipakai di create & edit) --}}
{{-- Variabel yang diharapkan: $hafalan (nullable), $santris, $surahs --}}

{{-- Error summary --}}
@if ($errors->any())
  <div class="alert error">
    <strong>Form belum valid:</strong>
    <ul style="margin:6px 0 0 18px">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  {{-- SANTRI --}}
  <div>
    <label>Santri</label>
    <select name="santri_id" required>
      <option value="">— Pilih Santri —</option>
      @foreach($santris as $s)
        <option value="{{ $s->id }}"
          @selected(old('santri_id', $hafalan->santri_id ?? null) == $s->id)>
          {{ $s->nama }} ({{ $s->nis }})
        </option>
      @endforeach
    </select>
    @error('santri_id') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  {{-- SURAH --}}
  <div>
    <label>Surah</label>
    <select name="surah_id" required>
      <option value="">— Pilih Surah —</option>
      @foreach($surahs as $s)
        <option value="{{ $s->id }}"
          @selected(old('surah_id', $hafalan->surah_id ?? null) == $s->id)>
          {{ $s->nomor }}. {{ $s->nama_id }}
        </option>
      @endforeach
    </select>
    @error('surah_id') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  {{-- TANGGAL SETOR --}}
  <div>
    <label>Tanggal Setor</label>
    <input type="date" name="tanggal_setor"
           value="{{ old('tanggal_setor', optional($hafalan->tanggal_setor ?? null)->format('Y-m-d') ?? now()->toDateString()) }}"
           required>
    @error('tanggal_setor') <div class="alert error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="row">
  {{-- AYAT MULAI --}}
  <div>
    <label>Ayat Mulai</label>
    <input type="number" name="ayat_mulai"
           value="{{ old('ayat_mulai', $hafalan->ayat_mulai ?? '') }}"
           min="1" required>
    @error('ayat_mulai') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  {{-- AYAT SELESAI --}}
  <div>
    <label>Ayat Selesai</label>
    <input type="number" name="ayat_selesai"
           value="{{ old('ayat_selesai', $hafalan->ayat_selesai ?? '') }}"
           min="1" required>
    @error('ayat_selesai') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  {{-- STATUS --}}
  <div>
    <label>Status</label>
    <select name="status" required>
      <option value="tuntas" @selected(old('status', $hafalan->status ?? '')==='tuntas')>Tuntas</option>
      <option value="belum"  @selected(old('status', $hafalan->status ?? '')==='belum')>Belum</option>
    </select>
    @error('status') <div class="alert error">{{ $message }}</div> @enderror
  </div>
</div>

{{-- CATATAN --}}
<div style="margin-top:12px">
  <label>Catatan</label>
  <textarea name="catatan" rows="3">{{ old('catatan', $hafalan->catatan ?? '') }}</textarea>
  @error('catatan') <div class="alert error">{{ $message }}</div> @enderror
</div>
@if ($errors->any())
  <div class="alert error">
    <strong>Periksa kembali:</strong>
    <ul style="margin:6px 0 0 18px">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  <div>
    <label>NIS</label>
    <input type="text" name="nis" value="{{ old('nis', $santri->nis ?? '') }}" required>
    @error('nis') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  <div>
    <label>Nama</label>
    <input type="text" name="nama" value="{{ old('nama', $santri->nama ?? '') }}" required>
    @error('nama') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  <div>
    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin" required>
      <option value="L" @selected(old('jenis_kelamin', $santri->jenis_kelamin ?? '')==='L')>L</option>
      <option value="P" @selected(old('jenis_kelamin', $santri->jenis_kelamin ?? '')==='P')>P</option>
    </select>
    @error('jenis_kelamin') <div class="alert error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="row">
  <div>
    <label>Angkatan</label>
    <select name="angkatan_id" required>
      <option value="">— Pilih Angkatan —</option>
      @foreach($angkatanList as $a)
        <option value="{{ $a->id }}" @selected(old('angkatan_id', $santri->angkatan_id ?? null) == $a->id)>
          {{ $a->nama }}
        </option>
      @endforeach
    </select>
    @error('angkatan_id') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  <div>
    <label>Kelas</label>
    <select name="kelas_id" required>
      <option value="">— Pilih Kelas —</option>
      @foreach($kelasList as $k)
        <option value="{{ $k->id }}" @selected(old('kelas_id', $santri->kelas_id ?? null) == $k->id)>
          {{ $k->nama }}
        </option>
      @endforeach
    </select>
    @error('kelas_id') <div class="alert error">{{ $message }}</div> @enderror
  </div>

  <div>
    <label>Strata (opsional)</label>
    <select name="strata_id">
      <option value="">— Kosongkan —</option>
      @foreach($strataList as $s)
        <option value="{{ $s->id }}" @selected(old('strata_id', $santri->strata_id ?? null) == $s->id)>
          {{ $s->nama }} ({{ $s->jenis_kelas }})
        </option>
      @endforeach
    </select>
    @error('strata_id') <div class="alert error">{{ $message }}</div> @enderror
  </div>
</div>
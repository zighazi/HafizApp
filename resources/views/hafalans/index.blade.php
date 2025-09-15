@extends('layouts.app')
@section('title','Daftar Hafalan')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Daftar Hafalan</h1>
    <a href="{{ route('hafalans.create') }}" class="btn btn-primary">Tambah Hafalan</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:56px">#</th>
              <th>Nama Santri</th>
              <th>Surah</th>
              <th>Ayat</th> {{-- rentang: awal–akhir --}}
              <th style="width:160px">Tanggal</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($hafalans as $i => $h)
              @php
                // Ambil nilai yang aman untuk array ataupun Eloquent object:
                $namaSantri = data_get($h, 'santri.nama')           // relasi Eloquent: $h->santri->nama
                              ?? data_get($h, 'santri_nama')        // field alias di dummy
                              ?? data_get($h, 'nama')                // fallback
                              ?? '-';

                $surahNama  = data_get($h, 'surah.nama')            // relasi Eloquent: $h->surah->nama
                              ?? data_get($h, 'surah_nama')
                              ?? data_get($h, 'surah')
                              ?? '-';

                // Rentang ayat: ayat_awal–ayat_akhir; fallback ke 'ayat' jika belum pakai rentang
                $ayatAwal   = data_get($h, 'ayat_awal');
                $ayatAkhir  = data_get($h, 'ayat_akhir');
                $ayatLabel  = ($ayatAwal && $ayatAkhir)
                              ? ($ayatAwal.'–'.$ayatAkhir)
                              : (data_get($h, 'ayat') ?? '-');

                // Tanggal (string atau Carbon)
                $tanggalVal = data_get($h, 'tanggal');
                try {
                  // jika Carbon instance, format; jika string, tampilkan apa adanya
                  $tanggal = $tanggalVal instanceof \Carbon\Carbon
                    ? $tanggalVal->toDateString()
                    : (is_string($tanggalVal) ? $tanggalVal : '-');
                } catch (\Throwable $e) {
                  $tanggal = is_string($tanggalVal) ? $tanggalVal : '-';
                }

                // Penomoran jika memakai pagination vs koleksi biasa
                $rowNumber = method_exists($hafalans, 'firstItem')
                              ? $hafalans->firstItem() + $loop->index
                              : ($i + 1);
              @endphp

              <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $namaSantri }}</td>
                <td>{{ $surahNama }}</td>
                <td>{{ $ayatLabel }}</td>
                <td>{{ $tanggal }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Pagination (otomatis tampil jika $hafalans adalah paginator) --}}
  @if(method_exists($hafalans, 'links'))
    <div class="mt-3">
      {{ $hafalans->links() }}
    </div>
  @endif
@endsection
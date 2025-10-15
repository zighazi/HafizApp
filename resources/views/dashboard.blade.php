@extends('layouts.app')
@section('title','Beranda')
@section('page','Beranda')

@section('content')
  @php
    // Default aman jika variabel belum dikirim controller
    $totalSantri   = $totalSantri   ?? 0;
    $totalHafalan  = $totalHafalan  ?? 0;
    $kelasAktif    = $kelasAktif    ?? 0;

    // Pastikan $rekap & $santris berupa koleksi
    $rekap   = \Illuminate\Support\Collection::wrap($rekap ?? []);
    $santris = \Illuminate\Support\Collection::wrap($santris ?? []);

    // Untuk chart batang: normalisasi tinggi berdasarkan maksimum
    $maxRekap = max(1, (int) $rekap->max('total'));
  @endphp

  <div class="grid md:grid-cols-3 gap-4">
    <x-stat-card label="Total Santri"  :value="$totalSantri"  :hint="'Semua angkatan'" />
    <x-stat-card label="Total Hafalan" :value="$totalHafalan" />
    <x-stat-card label="Kelas Aktif"   :value="$kelasAktif"   />
  </div>

  <div class="mt-6 grid lg:grid-cols-3 gap-4">
    {{-- Rekap 6 bulan --}}
    <x-card title="Rekap 6 Bulan Terakhir">
      @if ($rekap->isEmpty())
        <div class="text-sm text-gray-500 dark:text-gray-400">Belum ada data.</div>
      @else
        <div class="grid grid-cols-6 gap-2 text-xs">
          @foreach ($rekap as $r)
            @php
              $bulan = is_array($r) ? ($r['bulan'] ?? '') : ($r->bulan ?? '');
              $tot   = (int) (is_array($r) ? ($r['total'] ?? 0) : ($r->total ?? 0));
              // tinggi batang 8–100% dari container
              $h     = max(8, (int) round(($tot / $maxRekap) * 100));
            @endphp
            <div class="text-center">
              <div class="rounded-lg h-24 border border-gray-200 dark:border-gray-800 flex items-end justify-center pb-2">
                <div class="w-6" style="height: {{ $h }}%"></div>
              </div>
              <div class="mt-1">
                {{ \Illuminate\Support\Str::of($bulan)->replace('-','/') }}
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </x-card>

    {{-- Santri terbaru --}}
    <x-card class="lg:col-span-2" title="Santri Terbaru">
      @if ($santris->isEmpty())
        <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data.</div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left border-t border-b border-gray-200 dark:border-gray-800">
              <tr>
                <th class="p-3">NIS</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Kelas</th>
                <th class="p-3">Strata</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($santris as $s)
                @php
                  // dukung object Eloquent maupun array biasa
                  $nis    = is_array($s) ? ($s['nis'] ?? '')  : ($s->nis ?? '');
                  $nama   = is_array($s) ? ($s['nama'] ?? '') : ($s->nama ?? '');
                  $kelas  = is_array($s)
                              ? ($s['kelas'] ?? '-')
                              : ($s->kelas?->nama ?? ($s->kelas ?? '-'));   // relasi?->nama, fallback kolom teks
                  $strata = is_array($s)
                              ? ($s['strata'] ?? '-')
                              : ($s->strata?->nama ?? ($s->strata ?? '-'));
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                  <td class="p-3">{{ $nis }}</td>
                  <td class="p-3">{{ $nama }}</td>
                  <td class="p-3">{{ $kelas }}</td>
                  <td class="p-3">{{ $strata }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </x-card>
  </div>
@endsection
@extends('layouts.app')

@section('title', 'Dashboard Orangtua')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Dashboard Orangtua</h1>
        <button id="themeToggle" class="px-3 py-2 rounded-lg border">🌙 / ☀️</button>
    </div>

    {{-- Filter Santri --}}
    <form method="GET" action="{{ route('parent.dashboard') }}" class="mb-6">
        <label class="block text-sm font-medium mb-1">Pilih Santri</label>
        <div class="flex gap-3">
            <select name="santri_id" class="border rounded-lg px-3 py-2 w-full max-w-md">
                @foreach ($santris as $s)
                    <option value="{{ $s->nis }}" @selected(optional($activeSantri)->nis === $s->nis)>
                        {{ $s->nama }} ({{ $s->nis }})
                    </option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Tampilkan</button>
        </div>
    </form>

    @if (!$activeSantri)
        <div class="p-4 border rounded-lg bg-yellow-50">
            Belum ada data santri. Silakan impor data santri terlebih dahulu.
        </div>
    @else
        {{-- ===== Filter Santri + Tanggal ===== --}}
<form method="GET" action="{{ route('parent.dashboard') }}" class="mb-6 space-y-3">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
    {{-- Pilih Santri --}}
    <div>
      <label class="block text-sm font-medium mb-1">Pilih Santri</label>
      <select name="santri_id" class="border rounded-lg px-3 py-2 w-full">
        @foreach ($santris as $s)
          <option value="{{ $s->nis }}" @selected(optional($activeSantri)->nis === $s->nis)>{{ $s->nama }} ({{ $s->nis }})</option>
        @endforeach
      </select>
    </div>

    {{-- Preset Range --}}
    <div>
      <label class="block text-sm font-medium mb-1">Rentang Waktu</label>
      <select name="preset" id="preset" class="border rounded-lg px-3 py-2 w-full">
        <option value="week"    @selected(($filters['preset'] ?? 'week')==='week')>Minggu ini</option>
        <option value="month"   @selected(($filters['preset'] ?? '')==='month')>Bulan ini</option>
        <option value="quarter" @selected(($filters['preset'] ?? '')==='quarter')>3 Bulan terakhir</option>
        <option value="custom"  @selected(($filters['preset'] ?? '')==='custom')>Custom</option>
      </select>
    </div>

    {{-- Custom Range (muncul jika preset=custom) --}}
    <div class="grid grid-cols-2 gap-3" id="customRange" style="display:none;">
      <div>
        <label class="block text-sm font-medium mb-1">Mulai</label>
        <input type="date" name="start" value="{{ $filters['start'] ?? '' }}" class="border rounded-lg px-3 py-2 w-full">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Selesai</label>
        <input type="date" name="end" value="{{ $filters['end'] ?? '' }}" class="border rounded-lg px-3 py-2 w-full">
      </div>
    </div>
  </div>

  <div class="flex gap-3">
    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Terapkan</button>
    <div class="px-3 py-2 text-sm rounded-lg border bg-gray-50">
      Rentang aktif: <strong>{{ $filters['label'] ?? 'Minggu ini' }}</strong>
    </div>
  </div>
</form>

<script>
  // tampilkan/hidden custom date picker
  const presetSel = document.getElementById('preset');
  const customDiv = document.getElementById('customRange');
  function toggleCustom() { customDiv.style.display = presetSel.value === 'custom' ? '' : 'none'; }
  presetSel?.addEventListener('change', toggleCustom);
  toggleCustom();
</script>
        {{-- Ringkasan Kartu --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm opacity-70">Setoran Minggu Ini</div>
                <div class="text-3xl font-bold">{{ $summary['week_sets'] }}</div>
            </div>
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm opacity-70">Baris Minggu Ini</div>
                <div class="text-3xl font-bold">{{ $summary['week_baris'] }}</div>
            </div>
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm opacity-70">Setoran Bulan Ini</div>
                <div class="text-3xl font-bold">{{ $summary['month_sets'] }}</div>
            </div>
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm opacity-70">Baris Bulan Ini</div>
                <div class="text-3xl font-bold">{{ $summary['month_baris'] }}</div>
            </div>

            {{-- NEW: Target Mingguan --}}
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm opacity-70 mb-1">Target Baris Mingguan</div>
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-bold">{{ $summary['target_baris_mingguan'] }}</div>
                    @if($summary['tuntas_mingguan'])
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 border border-green-300">Tuntas</span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 border border-red-300">Belum</span>
                    @endif
                </div>

                {{-- Progress bar --}}
                <div class="mt-3">
                    <div class="w-full h-2 rounded bg-gray-200 overflow-hidden">
                        <div class="h-2 rounded"
                             style="width: {{ $summary['progress_mingguan'] }}%; background: linear-gradient(90deg, #60a5fa, #34d399);">
                        </div>
                    </div>
                    <div class="mt-1 text-xs opacity-70">
                        {{ $summary['week_baris'] }} / {{ $summary['target_baris_mingguan'] }} baris ({{ $summary['progress_mingguan'] }}%)
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik 6 Bulan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm font-semibold mb-2">Jumlah Setoran (6 bulan terakhir)</div>
                <canvas id="setsChart" height="140"></canvas>
            </div>
            <div class="p-4 rounded-2xl border shadow-sm">
                <div class="text-sm font-semibold mb-2">Total Baris (6 bulan terakhir)</div>
                <canvas id="barisChart" height="140"></canvas>
            </div>
        </div>

        {{-- Riwayat Terbaru --}}
        <div class="p-4 rounded-2xl border shadow-sm">
            <div class="text-sm font-semibold mb-3">Riwayat Setoran Terbaru</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 pr-4">Tanggal</th>
                            <th class="text-left py-2 pr-4">Surah</th>
                            <th class="text-left py-2 pr-4">Ayat</th>
                            <th class="text-left py-2 pr-4">Baris</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $row)
                            <tr class="border-b last:border-0">
                                <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                                <td class="py-2 pr-4">{{ $row->surah_nama ?? '—' }}</td>
                                <td class="py-2 pr-4">
                                    @if(!is_null($row->ayat_mulai) && !is_null($row->ayat_selesai))
                                        {{ $row->ayat_mulai }}–{{ $row->ayat_selesai }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-2 pr-4">{{ (int) $row->baris }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-3" colspan="4">Belum ada data setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('themeToggle');
    btn?.addEventListener('click', () => {
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
    });
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }

    @if ($activeSantri)
    const res = await fetch(`{{ route('parent.dashboard.api', ['santri' => $activeSantri->nis]) }}`);
    const data = await res.json();

    const setsCtx = document.getElementById('setsChart').getContext('2d');
    new Chart(setsCtx, {
        type: 'bar',
        data: { labels: data.labels, datasets: [{ label: 'Setoran', data: data.sets }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    const barisCtx = document.getElementById('barisChart').getContext('2d');
    new Chart(barisCtx, {
        type: 'line',
        data: { labels: data.labels, datasets: [{ label: 'Baris', data: data.baris }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    @endif
});
</script>

<style>
:root { color-scheme: light dark; }
html.dark body { background-color: #0b1220; color: #e5e7eb; }
html.dark .border { border-color: #1f2937; }
html.dark .bg-yellow-50 { background-color: #3b2f00; }
html.dark .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,.4); }
</style>
@endsection
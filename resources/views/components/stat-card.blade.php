@props(['label','value','delta'=>null,'hint'=>null])
<div class="rounded-2xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
  <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
  <div class="mt-1 text-2xl font-semibold">{{ $value }}</div>
  <div class="mt-1 text-xs {{ ($delta ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
    @if(!is_null($delta)) {{ $delta>=0?'+':'' }}{{ $delta }}% vs bln lalu @endif
  </div>
  @if($hint)<div class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</div>@endif
</div>
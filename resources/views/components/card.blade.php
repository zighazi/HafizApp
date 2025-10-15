@props(['title'=>null,'actions'=>null])
<div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
  @if($title || $actions)
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
      <div class="font-medium">{{ $title }}</div>
      <div>{{ $actions }}</div>
    </div>
  @endif
  <div class="p-4">{{ $slot }}</div>
</div>
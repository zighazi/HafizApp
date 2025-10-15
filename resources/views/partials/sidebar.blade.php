<aside class="hidden md:block w-64 shrink-0 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
  <div class="p-4 flex items-center gap-2 border-b border-gray-200 dark:border-gray-800">
    <div class="text-xl">✦</div>
    <div class="font-semibold">HafizApp</div>
  </div>
  <nav class="p-2">
    @php
      $items = [
        ['label'=>'Beranda','route'=>'home','icon'=>'home'],
        ['label'=>'Hafalan','route'=>'hafalans.index','icon'=>'book-open'],
        ['label'=>'Rekap Bulanan','route'=>'rekap.kelas','icon'=>'bar-chart-2'],
        ['label'=>'Import Santri','route'=>'santris.index','icon'=>'upload'],
        ['label'=>'Profil','route'=>'profile','icon'=>'user'],
      ];
    @endphp
    @foreach($items as $it)
      @php $active = url()->current() === route($it['route'],[],false); @endphp
      <a href="{{ route($it['route']) }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                {{ $active ? 'bg-gray-100 dark:bg-gray-800 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
        @include('partials.icon', ['name'=>$it['icon']])
        <span>{{ $it['label'] }}</span>
      </a>
    @endforeach
  </nav>
</aside>
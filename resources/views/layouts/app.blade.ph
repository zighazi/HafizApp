<!doctype html>
<html lang="id" class="h-full"
      x-data="{dark: localStorage.theme==='dark'}"
      x-init="$watch('dark', v => {localStorage.theme = v?'dark':'light'; document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', localStorage.theme==='dark')">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <title>@yield('title','HafizApp')</title>
</head>
<body class="h-full bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
  <header class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
    <div class="font-semibold">@yield('page','Dashboard')</div>
    <button class="px-3 py-2 rounded-lg border text-sm border-gray-300 dark:border-gray-700" @click="dark=!dark">
      <span x-show="!dark">🌙 Dark</span>
      <span x-show="dark">☀️ Light</span>
    </button>
  </header>
  <main class="p-4">@yield('content')</main>
</body>
</html>
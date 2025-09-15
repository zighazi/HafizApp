protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
{
    // contoh: jalankan 30 Juli tiap tahun, set tahun ajaran otomatis
    $schedule->command(function () {
        $y = now()->year; // 2025
        $next = $y.'/'.($y+1); // "2025/2026"
        return "hafizapp:promote {$next}";
    })->yearlyOn(7, 30, '00:05'); // 30 Juli 00:05
}

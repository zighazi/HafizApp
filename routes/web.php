<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HafalanController;
use App\Http\Controllers\RekapKelasController;
use App\Http\Controllers\SantriImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| File ini sengaja dibuat ringkas & anti-bentrok:
| - Tidak ada group bersarang ganda
| - Penamaan route konsisten (rekap.kelas, rekap.kelas.bulanan, dst)
| - Hanya memanggil method controller yang memang tersedia
*/

/** -----------------------------------------------------------------
 *  Beranda (landing page)
 *  Pastikan view resources/views/landing.blade.php tersedia.
 *  -----------------------------------------------------------------*/
Route::view('/', 'landing')->name('home');

/** -----------------------------------------------------------------
 *  Hafalan (pakai Nama Santri, Surah, Ayat Awal–Akhir)
 *  Method yang dipakai: index, create, store
 *  -----------------------------------------------------------------*/
Route::resource('hafalans', HafalanController::class)->only(['index', 'create', 'store']);

/** -----------------------------------------------------------------
 *  Import Santri (form + submit)
 *  Pastikan SantriImportController@form & @import ada.
 *  -----------------------------------------------------------------*/
Route::get('/santris', fn () => redirect()->route('santris.import.form'))->name('santris.index');
Route::get('/santris/import', [SantriImportController::class, 'form'])->name('santris.import.form');
Route::post('/santris/import', [SantriImportController::class, 'import'])->name('santris.import');

/** -----------------------------------------------------------------
 *  Rekap Kelas
 *  - /rekap/kelas                -> index()  (mengarah ke bulanan)
 *  - /rekap/kelas/bulanan        -> bulanan()
 *  - /rekap/kelas/bulanan/export -> exportBulananCsv()
 *  -----------------------------------------------------------------*/
Route::prefix('rekap')->name('rekap.')->group(function () {
    Route::get('/kelas', [RekapKelasController::class, 'index'])->name('kelas');
    Route::get('/kelas/bulanan', [RekapKelasController::class, 'bulanan'])->name('kelas.bulanan');
    Route::get('/kelas/bulanan/export', [RekapKelasController::class, 'exportBulananCsv'])->name('kelas.bulanan.export');

    Route::get('/kelas/tahunan', [RekapKelasController::class, 'tahunan'])->name('kelas.tahunan');
    Route::get('/kelas/tahunan/export', [RekapKelasController::class, 'exportTahunanCsv'])->name('kelas.tahunan.export');
    Route::get('/kelas/tahunan/print',  [RekapKelasController::class, 'printTahunan'])->name('kelas.tahunan.print');
});
/** -----------------------------------------------------------------
 *  Alias kompatibilitas (kalau ada menu lama)
 *  /rekap-kelas -> rekap.kelas
 *  -----------------------------------------------------------------*/
Route::get('/rekap-kelas', fn () => redirect()->route('rekap.kelas'))->name('rekap-kelas.index');

/** -----------------------------------------------------------------
 *  Fallback 404 sederhana (opsional; hapus jika punya handler sendiri)
 *  -----------------------------------------------------------------*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
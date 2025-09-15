<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HafalanController;
use App\Http\Controllers\RekapKelasController;
use App\Http\Controllers\SantriImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Pastikan controller2 di atas ada. Jika nama method berbeda,
| samakan dengan yang digunakan di route (mis. form() pada SantriImportController).
*/

// Halaman depan -> ke daftar hafalan
Route::view('/', 'landing')->name('home');

// Index santris (redirect ke halaman import)
Route::get('/santris', fn () => redirect()->route('santris.import.form'))->name('santris.index');

/**
 * CRUD Hafalan (Blade)
 */
Route::resource('hafalans', HafalanController::class);

/**
 * Rekap Kelas (index + berbagai export)
 * Nama utama: rekap.kelas
 */
Route::prefix('rekap')->name('rekap.')->group(function () {
    Route::get('/kelas', [RekapKelasController::class, 'index'])->name('kelas');

    // Export CSV
    Route::get('/kelas/export', [RekapKelasController::class, 'export'])->name('kelas.export');
    Route::get('/kelas/export/harian', [RekapKelasController::class, 'exportHarian'])->name('kelas.export.harian');
    Route::get('/kelas/export/bulanan', [RekapKelasController::class, 'exportBulanan'])->name('kelas.export.bulanan');

    // “PDF” = halaman cetak
    Route::get('/kelas/export/pdf', [RekapKelasController::class, 'exportPdf'])->name('kelas.export.pdf');
    Route::get('/kelas/export/harian/pdf', [RekapKelasController::class, 'exportHarianPdf'])->name('kelas.export.harian.pdf');
    Route::get('/kelas/export/bulanan/pdf', [RekapKelasController::class, 'exportBulananPdf'])->name('kelas.export.bulanan.pdf');
});

/**
 * Alias kompatibilitas untuk navbar lama:
 * route('rekap-kelas.index') akan redirect ke route baru rekap.kelas
 */
Route::get('/rekap-kelas', fn () => redirect()->route('rekap.kelas'))->name('rekap-kelas.index');

/**
 * Import Santri (form + submit)
 */
Route::get('/santris/import', [SantriImportController::class, 'form'])->name('santris.import.form');
Route::post('/santris/import', [SantriImportController::class, 'import'])->name('santris.import');
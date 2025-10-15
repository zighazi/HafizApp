<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    HafalanController,
    RekapKelasController,
    SantriController,
    ProfileController,
    ParentDashboardController,
    UserAdminController, // ⬅️ admin: manajemen user
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ping/healthcheck
Route::get('/ping', fn () => 'pong');

// beranda publik
Route::get('/', [HomeController::class, 'index'])->name('home');

// ----- Semua halaman aplikasi (wajib login) -----
Route::middleware('auth')->group(function () {

    // Redirect dashboard sesuai role
    Route::get('/dashboard', function () {
        $u = auth()->user();
        if ($u && in_array($u->role, ['admin', 'orangtua'])) {
            return redirect()->route('parent.dashboard');
        }
        return redirect()->route('hafalans.index');
    })->name('dashboard');

    /** ---------------- Hafalan ---------------- */
    Route::resource('hafalans', HafalanController::class)
        ->only(['index', 'create', 'store']);

    /** ---------------- Santri (CRUD + Import/Export) ---------------- */
    Route::resource('santri', SantriController::class);
    Route::get('santri-export', [SantriController::class,'export'])
        ->middleware('throttle:30,1')
        ->name('santri.export');
    Route::get('santri-import', [SantriController::class,'importForm'])
        ->name('santri.import.form');
    Route::post('santri-import', [SantriController::class,'importStore'])
        ->middleware('throttle:20,1')
        ->name('santri.import.store');

    // Alias kompatibilitas link lama
    Route::get('/santris', fn () => redirect()->route('santri.index'))
        ->name('santris.index');

    /** ---------------- Rekap Kelas ---------------- */
    Route::prefix('rekap')->name('rekap.')->group(function () {
        Route::get('/kelas', [RekapKelasController::class, 'index'])->name('kelas');

        // Bulanan
        Route::get('/kelas/bulanan', [RekapKelasController::class, 'bulanan'])
            ->name('kelas.bulanan');
        Route::get('/kelas/bulanan/export', [RekapKelasController::class, 'exportBulananCsv'])
            ->middleware('throttle:30,1')
            ->name('kelas.bulanan.export');

        // Tahunan (opsional)
        Route::get('/kelas/tahunan', [RekapKelasController::class, 'tahunan'])
            ->name('kelas.tahunan');
        Route::get('/kelas/tahunan/export', [RekapKelasController::class, 'exportTahunanCsv'])
            ->middleware('throttle:30,1')
            ->name('kelas.tahunan.export');
        Route::get('/kelas/tahunan/print', [RekapKelasController::class, 'printTahunan'])
            ->name('kelas.tahunan.print');
    });

    /** ---------------- Dashboard Orangtua ----------------
     *  Halaman monitor progres hafalan per-santri (ringkasan + grafik)
     */
    Route::middleware(['role:admin,orangtua'])->group(function () {
        Route::get('/dashboard-orangtua', [ParentDashboardController::class, 'index'])
            ->name('parent.dashboard');

        // Endpoint JSON data grafik 6 bulan terakhir (binding ke kolom NIS)
        Route::get('/api/orangtua/hafalan/{santri:nis}', [ParentDashboardController::class, 'apiHafalan'])
            ->middleware('throttle:60,1') // rate-limit API
            ->name('parent.dashboard.api');
    });

    /** ---------------- Admin: Manajemen Pengguna ---------------- */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UserAdminController::class, 'update'])->name('users.update');
    });

    /** ---------------- Profile ---------------- */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');                    // nama lama
    Route::get('/profile-edit', fn () => redirect()->route('profile'))->name('profile.edit');       // alias Breeze
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (login/register/forgot password, dll.)
require __DIR__ . '/auth.php';

// (Opsional) 404 fallback yang rapih untuk route tidak dikenal (hanya di production)
// if (app()->environment('production')) {
//     Route::fallback(fn () => abort(404));
// }
<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

Auth::routes();

// Offline fallback page (cached by service worker)
Route::get('/offline', fn() => view('offline'))->name('offline');

// Public customer report access via signed URL (no auth required)
Route::get('/c/{customer}/laporan', [CustomerController::class, 'publicLaporan'])
    ->middleware('throttle:public-laporan')
    ->name('customers.public-laporan');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Profile — signature setup (must be before signature.required middleware group)
    Route::get('/profile/signature', [ProfileController::class, 'showSignatureSetup'])->name('profile.signature.show');
    Route::post('/profile/signature', [ProfileController::class, 'storeSignature'])->name('profile.signature.store');
    Route::get('/profile/password',  [ProfileController::class, 'showPasswordEdit'])->name('profile.password.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Notifications (teknisi only)
    Route::middleware('role:teknisi')->group(function () {
        Route::get('/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });

    Route::middleware('signature.required')->group(function () {

    // Dashboards
    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])->middleware('role:admin|sales')->name('dashboard.sales');
    Route::get('/dashboard/teknisi/all', [DashboardController::class, 'teknisiAll'])->middleware('role:admin|sales|teknisi')->name('dashboard.teknisi.all');
    Route::get('/dashboard/teknisi/my', [DashboardController::class, 'teknisiMy'])->middleware('role:teknisi')->name('dashboard.teknisi.my');
    Route::get('/dashboard/customer', [DashboardController::class, 'customer'])->middleware('role:customer')->name('dashboard.customer');
    // ── Tugas (Tasks) ─────────────────────────────────────────────────
    Route::get('tugas', [TugasController::class, 'index'])->name('tugas.index');
    // Explicit paths must come before {tugas} wildcard
    Route::get('tugas/create', [TugasController::class, 'create'])->middleware('role:admin|sales')->name('tugas.create');
    Route::post('tugas', [TugasController::class, 'store'])->middleware('role:admin|sales')->name('tugas.store');
    Route::get('tugas/{tugas}', [TugasController::class, 'show'])->name('tugas.show');
    Route::patch('tugas/{tugas}/start', [TugasController::class, 'start'])->middleware('role:teknisi')->name('tugas.start');
    Route::get('tugas/{tugas}/edit', [TugasController::class, 'edit'])->middleware('role:admin|sales')->name('tugas.edit');
    Route::put('tugas/{tugas}', [TugasController::class, 'update'])->middleware('role:admin|sales')->name('tugas.update');
    Route::patch('tugas/{tugas}', [TugasController::class, 'update'])->middleware('role:admin|sales');
    Route::delete('tugas/{tugas}', [TugasController::class, 'destroy'])->middleware('role:admin|sales')->name('tugas.destroy');

    // ── Laporan (Reports) ─────────────────────────────────────────────
    // Explicit paths must come before {laporan} wildcard
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/create', [LaporanController::class, 'create'])->middleware('role:teknisi')->name('laporan.create');
    Route::post('laporan', [LaporanController::class, 'store'])->middleware('role:teknisi')->name('laporan.store');
    Route::get('laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::patch('laporan/{laporan}', [LaporanController::class, 'update']);
    Route::delete('laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::get('laporan/{laporan}/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
    Route::get('laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');

    // ── Template Builder ──────────────────────────────────────────────
    Route::get('/template', [TemplateController::class, 'index'])->name('template.index');
    Route::get('/template/{template}', [TemplateController::class, 'show'])->name('template.show');
    Route::post('/template', [TemplateController::class, 'store'])->middleware('role:admin|sales')->name('template.store');
    Route::delete('/template/{template}', [TemplateController::class, 'destroy'])->middleware('role:admin|sales')->name('template.destroy');

    // ── Users (admin only) ────────────────────────────────────────────
    Route::middleware('can:view users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // ── Customers ─────────────────────────────────────────────────────
    Route::middleware('can:view customers')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('customers/{customer}/laporan', [CustomerController::class, 'laporan'])
            ->middleware('can:view customer reports')
            ->name('customers.laporan');
    });

    }); // end signature.required group
});

<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Dashboard
    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])->name('dashboard.sales');
    Route::get('/dashboard/teknisi', [DashboardController::class, 'teknisi'])->name('dashboard.teknisi');

    // Tugas
    Route::resource('tugas', TugasController::class);

    // Laporan
    Route::resource('laporan', LaporanController::class);

    // Template
    Route::get('/template', [TemplateController::class, 'index'])->name('template.index');

    // User management (admin only)
    Route::middleware('can:view users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Customer management
    Route::middleware('can:view customers')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('customers/{customer}/laporan', [CustomerController::class, 'laporan'])
            ->middleware('can:view customer reports')
            ->name('customers.laporan');
    });
});

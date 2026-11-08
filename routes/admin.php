<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\AdminResponseHeaders;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(AdminResponseHeaders::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth', 'admin.active'])->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');

        Route::middleware('can:manage-batches')->group(function () {
            Route::get('batches/create', [BatchController::class, 'create'])->name('batches.create');
            Route::post('batches', [BatchController::class, 'store'])->name('batches.store');
            Route::get('batches/{batch}/edit', [BatchController::class, 'edit'])->name('batches.edit');
            Route::put('batches/{batch}', [BatchController::class, 'update'])->name('batches.update');
            Route::post('batches/{batch}/open', [BatchController::class, 'open'])->name('batches.open');
            Route::post('batches/{batch}/close', [BatchController::class, 'close'])->name('batches.close');
            Route::get('settings', [BusinessSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');
        });
    });
});

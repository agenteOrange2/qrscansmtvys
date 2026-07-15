<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QrScanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/admin/dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', DashboardController::class)
        ->middleware('can:dashboard')
        ->name('dashboard');

    Route::middleware('can:scan')->group(function () {
        Route::get('scan', [QrScanController::class, 'scan'])->name('scan');
        Route::post('scan', [QrScanController::class, 'store'])->name('scan.store');
        Route::post('scan/grupo', [QrScanController::class, 'storeGroup'])->name('scan.store-group');
    });

    Route::middleware('can:captura')->group(function () {
        Route::get('usuarios-capturados/export', [QrScanController::class, 'export'])
            ->name('usuarios-capturados.export');
        Route::delete('usuarios-capturados', [QrScanController::class, 'destroyMany'])
            ->name('usuarios-capturados.destroy-many');
        Route::resource('usuarios-capturados', QrScanController::class)
            ->only(['index', 'edit', 'update', 'destroy'])
            ->parameters(['usuarios-capturados' => 'usuarios_capturado']);
    });

    Route::resource('marcas', MarcaController::class)
        ->except(['show'])
        ->middleware('can:scan');

    Route::middleware('can:users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('can:roles')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});

require __DIR__.'/settings.php';

<?php

use App\Http\Controllers\Admin\BitrixSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QrScanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/admin/dashboard')->name('dashboard');

    // Crea el symlink public/storage → storage/app/public desde el navegador
    // (el hosting es cPanel sin terminal, no se puede correr artisan).
    Route::get('storage_public', function () {
        abort_unless(auth()->user()?->hasRole('Administrador'), 403);

        $link = public_path('storage');

        // Symlink roto (p. ej. subido en el zip desde otro servidor): recrearlo
        if (is_link($link) && ! file_exists($link)) {
            unlink($link);
        }

        if (file_exists($link)) {
            return response()->json([
                'ok' => true,
                'message' => 'El enlace public/storage ya existe, no se hizo nada.',
                'target' => is_link($link) ? readlink($link) : $link,
            ]);
        }

        try {
            Artisan::call('storage:link');
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'No se pudo crear el enlace: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'ok' => file_exists($link),
            'message' => trim(Artisan::output()) ?: 'Enlace public/storage creado.',
            'target' => is_link($link) ? readlink($link) : null,
        ]);
    })->name('storage-public');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', DashboardController::class)
        ->middleware('can:dashboard')
        ->name('dashboard');

    Route::middleware('can:scan')->group(function () {
        Route::get('scan', [QrScanController::class, 'scan'])->name('scan');
        Route::post('scan', [QrScanController::class, 'store'])->name('scan.store');
        Route::post('scan/grupo', [QrScanController::class, 'storeGroup'])->name('scan.store-group');
        Route::post('scan/{scan}/reescanear', [QrScanController::class, 'rescan'])->name('scan.rescan');
    });

    Route::middleware('can:captura')->group(function () {
        Route::get('usuarios-capturados/export', [QrScanController::class, 'export'])
            ->name('usuarios-capturados.export');
        Route::delete('usuarios-capturados', [QrScanController::class, 'destroyMany'])
            ->name('usuarios-capturados.destroy-many');
        Route::post('usuarios-capturados/{usuarios_capturado}/reenviar-bitrix', [QrScanController::class, 'resendBitrix'])
            ->name('usuarios-capturados.resend-bitrix');
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

    Route::middleware('can:integraciones')->prefix('integraciones/bitrix')->name('integraciones.bitrix.')->group(function () {
        Route::get('/', [BitrixSettingController::class, 'edit'])->name('edit');
        Route::put('/', [BitrixSettingController::class, 'update'])->name('update');
        Route::post('probar', [BitrixSettingController::class, 'test'])->name('test');
        Route::post('pipelines', [BitrixSettingController::class, 'pipelines'])->name('pipelines');
        Route::post('etapas', [BitrixSettingController::class, 'stages'])->name('stages');
        Route::post('sincronizar', [BitrixSettingController::class, 'syncPending'])->name('sync');
    });
});

require __DIR__.'/settings.php';

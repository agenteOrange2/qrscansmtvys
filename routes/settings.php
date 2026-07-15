<?php

use App\Http\Controllers\Settings\BrandingController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
    Route::redirect('/', '/admin/settings/profile');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::inertia('appearance', 'settings/Appearance')->name('appearance.edit');

    Route::get('two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');

    Route::middleware('can:branding')->group(function () {
        Route::get('branding', [BrandingController::class, 'edit'])->name('branding.edit');
        Route::put('branding', [BrandingController::class, 'update'])->name('branding.update');
        Route::delete('branding/{asset}', [BrandingController::class, 'destroyAsset'])
            ->whereIn('asset', ['logo', 'icon', 'favicon'])
            ->name('branding.destroy-asset');
    });
});

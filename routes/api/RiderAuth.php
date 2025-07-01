<?php


use App\Http\Controllers\Api\Agent\Auth\RiderAuthController;
use App\Http\Controllers\Api\Agent\Auth\RiderProfileController;
use App\Http\Controllers\Api\Agent\Auth\RiderTwoFactorController;
use Illuminate\Support\Facades\Route;

// ======================
// Agent Auth Routes
// ======================

// Route::prefix('rider')->name('rider.')->group(function () {

//     // Basic Auth
//     Route::post('login', [RiderAuthController::class, 'login'])->middleware('throttle:admin-login');
//     Route::post('completeProfile',[RiderProfileController::class,'completeProfile'])->middleware('auth:rider-api');
//     Route::delete('delete-account', [RiderAuthController::class, 'deleteAccount'])->middleware('auth:rider-api');
//     // Two-Factor Authentication (2FA)
//     Route::post('verify-otp', [RiderTwoFactorController::class, 'verify']);
//     Route::post('otp/resend', [RiderTwoFactorController::class, 'resend']);
// });

Route::prefix('riders')->name('riders.')->group(function () {
    // Authentication Routes
    Route::post('login', [RiderAuthController::class, 'login'])
        ->name('auth.login')
        ->middleware('throttle:5,1'); // 5 attempts per minute

    Route::middleware('auth:rider-api')->group(function () {
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::post('', [RiderProfileController::class, 'completeProfile'])
                ->name('complete');
        });

        // Account Management
        Route::delete('', [RiderAuthController::class, 'deleteAccount'])
            ->name('destroy');

        Route::post('logout', [RiderAuthController::class, 'logout'])
            ->name('auth.logout');

        Route::post('refresh-token', [RiderAuthController::class, 'refreshToken'])
            ->name('auth.refresh');
    });

    // Two-Factor Authentication
    Route::prefix('otp')->name('otp.')->group(function () {
        Route::post('verify', [RiderTwoFactorController::class, 'verify'])
            ->name('verify');

        Route::post('resend', [RiderTwoFactorController::class, 'resend'])
            ->name('resend');
    });
});

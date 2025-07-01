<?php

use App\Http\Controllers\Api\Agent\Auth\DriverAuthController;
use App\Http\Controllers\Api\Agent\Auth\DriverProfileController;
use App\Http\Controllers\Api\Agent\Auth\DriverTwoFactorController;
use App\Http\Controllers\Api\Agent\Auth\RiderAuthController;
use App\Http\Controllers\Api\Agent\Auth\RiderProfileController;
use App\Http\Controllers\Api\Agent\Auth\RiderTwoFactorController;
use Illuminate\Support\Facades\Route;

// ======================
// Agent Auth Routes
// ======================

// Route::prefix('driver')->name('driver.')->group(function () {

//     // Basic Auth
//     Route::post('login', [DriverAuthController::class, 'login'])->middleware('throttle:driver-login');
//     Route::post('completeProfile',[DriverProfileController::class,'completeProfile'])->middleware('auth:driver-api');
//     Route::post('completeDriverCar',[DriverProfileController::class,'completeDriverCar'])->middleware('auth:driver-api');
    
//     Route::delete('delete-account', [DriverAuthController::class, 'deleteAccount'])->middleware('auth:driver-api');
//     // Two-Factor Authentication (2FA)
//     Route::post('verify-otp', [DriverTwoFactorController::class, 'verify']);
//     Route::post('otp/resend', [DriverTwoFactorController::class, 'resend']);
// });

/*
|--------------------------------------------------------------------------
| API Driver Routes
|--------------------------------------------------------------------------
|
| All routes related to driver authentication and profile management
|
*/

Route::prefix('drivers')->name('drivers.')->group(function () {
    // Authentication Routes
    Route::post('login', [DriverAuthController::class, 'login'])
        ->name('auth.login')
        ->middleware('throttle:5,1'); // 5 attempts per minute
    
    Route::middleware('auth:driver-api')->group(function () {
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::post('', [DriverProfileController::class, 'completeProfile'])
                ->name('complete');
                
            Route::post('vehicle', [DriverProfileController::class, 'completeDriverCar'])
                ->name('vehicle.store');
        });

        // Account Management
        Route::delete('', [DriverAuthController::class, 'deleteAccount'])
            ->name('destroy');
            
        Route::post('logout', [DriverAuthController::class, 'logout'])
            ->name('auth.logout');
            
        Route::post('refresh-token', [DriverAuthController::class, 'refreshToken'])
            ->name('auth.refresh');
    });

    // Two-Factor Authentication
    Route::prefix('otp')->name('otp.')->group(function () {
        Route::post('verify', [DriverTwoFactorController::class, 'verify'])
            ->name('verify');
            
        Route::post('resend', [DriverTwoFactorController::class, 'resend'])
            ->name('resend');
    });
});
<?php



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

use App\Http\Controllers\Driver\Auth\DriverAuthController;
use App\Http\Controllers\Driver\Auth\DriverProfileController;
use App\Http\Controllers\Driver\Auth\DriverTwoFactorController;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->name('driver.')->group(function () {
    // Authentication Routes
    Route::post('login', [DriverAuthController::class, 'login'])
        ->name('auth.login');
    // ->middleware('throttle:5,1'); // 5 attempts per minute

    // Route::middleware('auth:driver-api')->group(function () {
    Route::middleware(['auth:driver-api', 'driver.suspended'])->group(function () {
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::post('completion', [DriverProfileController::class, 'completeProfile'])
                ->name('complete');
            Route::middleware('driver.profile.completed')->group(function () {
                Route::get('/', [DriverProfileController::class, 'profile'])
                    ->name('show');
                Route::post('update', [DriverProfileController::class, 'updateProfile']);
               
            });
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

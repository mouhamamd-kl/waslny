<?php



// ======================
// Agent Auth Routes
// ======================

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Auth\AdminPasswordResetController;
use App\Http\Controllers\Admin\Auth\AdminProfileController;
use App\Http\Controllers\Admin\Auth\AdminTwoFactorController;
use App\Http\Controllers\Admin\Auth\AdminVerificationController;
use App\Http\Controllers\Admin\Auth\AuthAdminController;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // Basic Auth
    Route::post('test', function () {
        return Admin::get();
    });
    Route::middleware('auth:admin-api')->group(function () {
        Route::delete('delete-account', [AdminAuthController::class, 'deleteAccount']);
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('auth.logout');
        Route::post('2fa/disable', [AdminTwoFactorController::class, 'disable']);

        Route::get('profile',[AdminProfileController::class, 'profile']);
        Route::post('profile/update',[AdminProfileController::class, 'updateProfile']);
    });

    Route::post('login', [AdminAuthController::class, 'login']);

    // Password Reset
    Route::post('password/email', [AdminPasswordResetController::class, 'sendResetLink']);
    Route::post('password/reset', [AdminPasswordResetController::class, 'resetPassword']);

    // Verification
    Route::get('verified-success', fn() => view('admin.auth.verified-success'))->name('verification.success');
    Route::get('email/verify', fn() => response()->json(['message' => 'Email verification required.']))
        ->middleware('auth:admin-api')
        ->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', [AdminVerificationController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::post('email/resend', [AdminVerificationController::class, 'resendVerificationEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Two-Factor Authentication (2FA)
    Route::prefix('otp')->name('otp.')->group(function () {
        Route::post('verify', [AdminTwoFactorController::class, 'verify'])
            ->name('verify');

        Route::post('resend', [AdminTwoFactorController::class, 'resend'])
            ->name('resend');
    });
});

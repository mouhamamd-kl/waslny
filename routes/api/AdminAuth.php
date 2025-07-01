<?php

use App\Http\Controllers\Api\Admin\Auth\AdminPasswordResetController;
use App\Http\Controllers\Api\Admin\Auth\AdminVerificationController;
use App\Http\Controllers\Api\Admin\Auth\AuthAdminController;
use App\Http\Controllers\Api\Admin\Auth\AdminTwoFactorController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

// ======================
// Agent Auth Routes
// ======================
Route::prefix('admin')->name('admin.')->group(function () {

    // Basic Auth
    Route::post('login', [AuthAdminController::class, 'login'])->middleware('throttle:admin-login');
    Route::delete('delete-account', [AuthAdminController::class, 'deleteAccount'])->middleware('auth:admin-api');

    // Password Reset
    Route::post('password/email', [AdminPasswordResetController::class, 'sendResetLink']);
    Route::post('password/reset', [AdminPasswordResetController::class, 'resetPassword']);

    // Verification
    Route::get('verified-success', fn () => view('admin.auth.verified-success'))->name('verification.success');
    Route::get('email/verify', fn () => response()->json(['message' => 'Email verification required.']))
        ->middleware('auth:admin-api')
        ->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', [AdminVerificationController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::post('email/resend', [AdminVerificationController::class, 'resendVerificationEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Two-Factor Authentication (2FA)
    Route::post('verify-otp', [AdminTwoFactorController::class, 'verify']);
    Route::post('otp/resend', [AdminTwoFactorController::class, 'resend']);
    Route::post('2fa/disable', [AdminTwoFactorController::class, 'disable'])->middleware('auth:admin-api');
});

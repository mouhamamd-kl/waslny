<?php

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::middleware('auth:rider-api')->controller(PaymentMethodController::class)
    ->prefix('payment-methods/rider')
    ->name('rider.payment-method.')
    ->group(function () {
        Route::get('/', 'riderIndex')->name('index');
        Route::post('/search', 'riderSearch')->name('search');
        Route::get('/{payment_method}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware('auth:admin-api')->controller(PaymentMethodController::class)
    ->prefix('payment-methods/admin')
    // ->name('payment-method.')
    ->name('admin.payment-method.')
    ->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/search', 'adminSearch')->name('search');
        Route::prefix('{payment_method}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

<?php

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::controller(PaymentMethodController::class)
    ->prefix('payment-methods')
    ->name('rider.payment-method.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/search', 'search')->name('search');
        Route::get('/{payment_method}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(PaymentMethodController::class)
    ->prefix('payment-methods')
    // ->name('payment-method.')
    ->name('admin.payment-method.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::prefix('{payment_method}')->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

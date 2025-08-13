<?php

use App\Http\Controllers\RiderCouponController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:rider-api'])->controller(RiderCouponController::class)
    ->prefix('coupons/rider')
    ->name('rider.coupons.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{code}', 'store')->name('store');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

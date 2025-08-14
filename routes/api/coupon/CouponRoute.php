<?php

use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:admin-api'])->controller(CouponController::class)
    ->prefix('coupons')
    // ->name('coupons.') // Old route name 
    ->name('admin.coupons.')
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::group(['prefix' => '{coupon}', 'where' => ['coupon' => '[0-9]+']],function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

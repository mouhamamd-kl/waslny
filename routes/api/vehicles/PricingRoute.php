<?php

use App\Http\Controllers\CarManufacturerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin-api'])->controller(PricingController::class)
    ->prefix('service-level/pricings')
    ->name('service-level.pricing.') // Optional name prefix
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::prefix('{car_service_level_pricing}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

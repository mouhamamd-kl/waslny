<?php

use App\Http\Controllers\DriverStatusController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin-api'])->controller(DriverStatusController::class)
    ->prefix('driver-statuses/admin')
    // ->name('trip-status.')
    ->name('admin.driver-status.')
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::prefix('{driver_status}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });
    });

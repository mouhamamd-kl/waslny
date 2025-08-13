<?php

use App\Http\Controllers\CarModelController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible by anyone)
Route::middleware(['auth:driver-api'])->controller(CarModelController::class)
    ->prefix('car-models/driver')
    // ->name('car-models.')  // Corrected name prefix
    ->name('driver.car-models.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        // Route::post('/search', 'search')->name('search');
        Route::post('/search', 'driverSearch')->name('search');
        Route::get('/{car_model}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(CarModelController::class)
    ->prefix('car-models/admin')
    // ->name('car-models.')
    ->name('admin.car-models.')
    ->group(function () {
        Route::post('/search', 'adminSearch')->name('search');
        Route::post('/', 'store')->name('store');
        Route::prefix('{car_model}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

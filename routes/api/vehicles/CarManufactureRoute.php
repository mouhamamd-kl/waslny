<?php

use App\Http\Controllers\CarManufacturerController;
use App\Http\Controllers\CarModelController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible by anyone)
Route::middleware(['auth:driver-api'])->controller(CarManufacturerController::class)
    ->prefix('car-manufacturers/driver')
    // ->name('car-manufacturers.')
    ->name('driver.car-manufacturers.')
    ->group(function () {
        Route::get('/', 'riderIndex')->name('index');
        // Route::post('/search', 'search')->name('search');
        Route::post('/search', 'driverSearch')->name('search');
        Route::prefix('{car_manufacturer}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/car-models', [CarModelController::class, 'carManufactureIndex'])->name('car-models.index');
        });
    });

// Admin-protected routes (using middleware group)
Route::middleware(['auth:admin-api'])->controller(CarManufacturerController::class)
    ->prefix('car-manufacturers/admin')
    // ->name('car-manufacturers.')
    ->name('admin.car-manufacturers.')
    ->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::post('/search', 'adminSearch')->name('search');
        Route::post('/', 'store')->name('store');
        Route::prefix('{car_manufacturer}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

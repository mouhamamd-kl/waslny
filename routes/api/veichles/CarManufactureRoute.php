<?php

use App\Http\Controllers\CarManufacturerController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible by anyone)
Route::controller(CarManufacturerController::class)
    ->prefix('car-manufacturers')
    ->name('car-manufacturers.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'search')->name('search');
        Route::get('/{car_manufacturer}', 'show')->name('show');
    });

// Admin-protected routes (using middleware group)
Route::middleware(['auth:admin-api'])->controller(CarManufacturerController::class)
    ->prefix('car-manufacturers')
    ->name('car-manufacturers.')
    ->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::post('/', 'store')->name('store');
        Route::prefix('{car_manufacturer}')->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
        Route::get('/search', 'adminSearch')->name('search');
    });

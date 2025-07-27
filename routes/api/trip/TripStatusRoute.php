<?php

use App\Http\Controllers\TripStatusController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin-api'])->controller(TripStatusController::class)
    ->prefix('trip-statuses')
    ->name('trip-status.')
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::prefix('{trip_status}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

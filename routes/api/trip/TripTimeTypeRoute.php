<?php

use App\Http\Controllers\TripTimeTypeController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::controller(TripTimeTypeController::class)
    ->prefix('trip-time-types')
    ->name('trip-time-type.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/search', 'search')->name('search');
        Route::get('/{trip_time_type}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(TripTimeTypeController::class)
    ->prefix('trip-time-types')
    ->name('trip-time-type.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
        
        Route::prefix('{trip_time_type}')->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

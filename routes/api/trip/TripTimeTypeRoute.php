<?php

use App\Http\Controllers\TripTimeTypeController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::middleware(['auth:rider-api'])->controller(TripTimeTypeController::class)
    ->prefix('trip-time-types/rider')
    // ->name('trip-time-type.')
    ->name('rider.trip-time-type.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        // Route::post('/search', 'search')->name('search');
        Route::post('/search', 'riderSearch')->name('search');
        Route::get('/{trip_time_type}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(TripTimeTypeController::class)
    ->prefix('trip-time-types/admin')
    // ->name('trip-time-type.')
    ->name('admin.trip-time-type.')
    ->group(function () {
        Route::post('/search', 'adminSearch')->name('search');
        Route::post('/', 'store')->name('store');
        Route::prefix('{trip_time_type}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

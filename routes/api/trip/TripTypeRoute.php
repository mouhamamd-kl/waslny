<?php

use App\Http\Controllers\TripTypeController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::controller(TripTypeController::class)
    ->prefix('trip-types')
    ->name('trip-type.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/search', 'search')->name('search');
        Route::get('/{trip_type}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(TripTypeController::class)
    ->prefix('trip-types')
    ->name('trip-type.')
    ->group(function () {
        Route::post('/', 'store')->name('store');

        Route::prefix('{trip_type}')->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

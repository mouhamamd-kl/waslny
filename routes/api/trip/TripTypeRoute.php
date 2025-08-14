<?php

use App\Http\Controllers\TripTypeController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible to all users)
Route::middleware(['auth:rider-api'])->controller(TripTypeController::class)
    ->prefix('trip-types/rider')
    // ->name('trip-type.')
    ->name('rider.trip-type.')
    ->group(function () {
        Route::get('/', 'riderIndex')->name('index');
        Route::post('/search', 'riderSearch')->name('search');
        Route::get('/{trip_type}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(TripTypeController::class)
    ->prefix('trip-types/admin')
    // ->name('trip-type.')
    ->name('admin.trip-type.')
    ->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::post('/search', 'adminSearch')->name('search');
        Route::post('/', 'store')->name('store');
        Route::prefix('{trip_type}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

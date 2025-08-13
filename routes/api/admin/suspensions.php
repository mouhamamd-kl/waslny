<?php

use App\Http\Controllers\SuspensionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin-api'])->controller(SuspensionController::class)
    ->prefix('suspensions')
    // ->name('suspensions.') // Optional name prefix
    ->name('admin.suspensions.')
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::prefix('{suspension}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

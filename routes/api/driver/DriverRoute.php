<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::controller(DriverController::class)
    ->prefix('drivers')
    ->name('drivers.')
    ->group(function () {
        Route::get('/{driver}', 'show')->name('show');
        // Route::put('/{driver}', 'update')->name('update');
    });

Route::middleware('auth:driver-api')->controller(DriverController::class)->prefix('driver')->name('driver.')->group(
    function () {
        Route::post('/update-location', 'updateLocation')->name('update-location');
        Route::post('/switch-to-online', 'SwitchToOnlineStatus')->name('switch-to-online');
        Route::post('/switch-to-offline', 'SwitchToOfflineStatus')->name('switch-to-offline');
    }
);

// Admin management routes (require admin authentication)
Route::middleware(['auth:admin-api'])
    ->controller(DriverController::class)
    ->prefix('drivers')
    ->name('admin.drivers.')   // Namespaced naming
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/search', 'search')->name('search');
        // Correct way to prefix route names
        Route::name('suspend.')->group(function () {
            Route::post('/{driver}/suspendForever', 'suspendForever')->name('forever'); // Full name: suspend.forever
            Route::post('/{driver}/suspendTemporarily', 'suspendTemporarily')->name('temporarily'); // Full name: suspend.temporarily
        });
        Route::post('/{driver}/reinstate', 'reinstate')->name('reinstate');
    });

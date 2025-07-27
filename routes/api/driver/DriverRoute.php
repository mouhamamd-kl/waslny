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

// Admin management routes (require admin authentication)
Route::middleware(['auth:admin-api'])
    ->controller(DriverController::class)
    ->prefix('drivers')
    ->name('admin.drivers.')   // Namespaced naming
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'search')->name('search');
        Route::post('/{driver}/suspendForever', 'suspendForever')->name('suspend');
        Route::post('/{driver}/suspendTemporarily', 'suspendTemporarily')->name('suspend');
        Route::post('/{driver}/reinstate', 'reinstate')->name('reinstate');
    });

<?php

use App\Http\Controllers\RiderController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
// Route::middleware(['auth:rider-api', 'rider-profile-completed'])->controller(RiderController::class)
//     ->prefix('rider')
//     ->name('rider.')
//     ->group(function () {
//         Route::get('/profile', 'show')->name('show');
//         Route::put('/profile', 'update')->name('update');
//     });

// Admin management routes (require admin authentication)
Route::middleware(['auth:admin-api'])
    ->controller(RiderController::class)
    ->prefix('riders')
    ->name('admin.riders.')   // Namespaced naming
    ->group(function () {
        Route::get('/{rider}', 'showAdmin')->name('show-admin');
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'search')->name('search');
        Route::post('/{rider}/suspendForever', 'suspendForever')->name('suspend');
        Route::post('/{rider}/suspendTemporarily', 'suspendTemporarily')->name('suspend');
        Route::post('/{rider}/reinstate', 'reinstate')->name('reinstate');
    });

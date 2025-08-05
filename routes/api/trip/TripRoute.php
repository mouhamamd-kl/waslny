<?php

use App\Http\Controllers\ScheduledTripController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Rider-specific routes
Route::middleware(['auth:rider-api'])
    ->controller(TripController::class)
    ->prefix('trips')
    ->name('trips.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/rider', 'riderIndex')->name('rider.index');
        Route::post('/cancel-by-rider', 'cancelTripByRider')->name('cancel.rider');
    });

// Driver-specific routes
Route::middleware(['auth:driver-api'])
    ->controller(TripController::class)
    ->prefix('trips')
    ->name('trips.')
    ->group(function () {
        Route::get('/driver', 'driverIndex')->name('driver.index');
        Route::post('/{trip}/complete', 'completeTrip')->name('complete');
        Route::post('/cancel-by-driver', 'cancelTripByDriver')->name('cancel.driver');
    });

// Shared routes (accessible by both riders and drivers)
Route::middleware(['auth:rider-api','auth:driver-api','auth:admin-api'])
    ->controller(TripController::class)
    ->prefix('trips')
    ->name('trips.')
    ->group(function () {
        Route::get('/{trip}', 'show')->name('show');
    });

// Admin-only routes
Route::middleware(['auth:admin-api'])
    ->controller(TripController::class)
    ->prefix('trips')
    ->name('admin.trips.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/search', 'search')->name('search');
        Route::put('/{trip}', 'update')->name('update');
        Route::delete('/{trip}', 'destroy')->name('destroy');
    });

// Backend-to-backend routes
Route::controller(TripController::class)
    ->prefix('trips')
    ->name('trips.')
    ->group(function () {
        Route::post('/find-driver', 'findDriverForTrip')->name('find-driver');
    });

Route::post('/trips/check-scheduled', [ScheduledTripController::class, 'checkScheduledTrips'])->name('trips.check_scheduled');

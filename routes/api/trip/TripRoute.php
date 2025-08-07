<?php

use App\Http\Controllers\ScheduledTripController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Trip API Routes
|--------------------------------------------------------------------------
|
| This file contains the API routes for trip management, organized
| for optimal Postman collection generation.
|
*/

Route::controller(TripController::class)
    ->prefix('trips')
    ->name('trips.')
    ->group(function () {
        //================================================================
        // Rider Trip Routes
        //================================================================
        Route::middleware(['auth:rider-api'])->group(function () {
            Route::get('/rider/list', 'riderIndex')->name('rider.index');
            Route::get('/rider/{trip}', 'show')->name('rider.show');
            Route::post('/rider/request', 'store')->name('rider.request');
            Route::post('/rider/cancel', 'cancelTripByRider')->name('rider.cancel');
        });

        //================================================================
        // Driver Trip Routes
        //================================================================
        Route::middleware(['auth:driver-api'])->group(function () {
            Route::get('/driver/list', 'driverIndex')->name('driver.index');
            Route::get('/driver/{trip}', 'show')->name('driver.show');
            Route::post('/driver/cancel', 'cancelTripByDriver')->name('driver.cancel');
            // Route::post('/driver/{trip}/complete', 'completeTrip')->name('driver.complete');
        });

        //================================================================
        // Admin Trip Routes
        //================================================================
        Route::middleware(['auth:admin-api'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{trip}', 'show')->name('show');
                Route::post('/search', 'search')->name('search');
                Route::put('/{trip}', 'update')->name('update');
                Route::delete('/{trip}', 'destroy')->name('destroy');
            });
    });

//================================================================
// Backend & Scheduled Routes (No Export to Postman)
//================================================================
Route::post('/trips/find-driver', [TripController::class, 'findDriverForTrip'])
    ->name('no-export.trips.find-driver');

Route::post('/trips/check-scheduled', [ScheduledTripController::class, 'checkScheduledTrips'])
    ->name('no-export.trips.check_scheduled');

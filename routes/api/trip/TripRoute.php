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
    ->group(function () {
        //================================================================
        // Rider Trip Routes
        //================================================================
        Route::middleware(['auth:rider-api'])
            ->prefix('rider')
            ->name('rider.trips.')
            ->group(function () {
                Route::get('/list', 'riderIndex')->name('index');
                Route::post('/search', 'searchRider')->name('search');
                Route::get('/{trip}', 'show')->name('show');
                Route::post('/request', 'store')->name('request');
                Route::post('/cancel', 'cancelTripByRider')->name('cancel');
            });

        //================================================================
        // Driver Trip Routes
        //================================================================
        Route::middleware(['auth:driver-api'])
            ->prefix('driver')
            ->name('driver.trips.')
            ->group(function () {
                Route::get('/list', 'driverIndex')->name('index');
                Route::post('/search', 'searchDriver')->name('search');
                Route::get('/{trip}', 'show')->name('show');
                Route::post('/cancel', 'cancelTripByDriver')->name('cancel');
                // Route::post('/driver/{trip}/complete', 'completeTrip')->name('driver.complete');
            });

        //================================================================
        // Admin Trip Routes
        //================================================================
        Route::middleware(['auth:admin-api'])
            ->prefix('admin')
            ->name('admin.trips.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{trip}', 'show')->name('show');
                Route::post('/search', 'searchAdmin')->name('search');
                Route::post('/{trip}', 'update')->name('update');
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

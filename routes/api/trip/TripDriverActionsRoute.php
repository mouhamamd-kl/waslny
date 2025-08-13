<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripDriverActionsController;

Route::middleware(['auth:driver-api'])
    ->controller(TripDriverActionsController::class)
    ->prefix('trips/driver/{id}')
    // ->name('trips.driver.')
    ->name('driver.trips.')
    ->group(function () {
        Route::post('/accept', 'accept')->name('accept');
        Route::post('/location', 'updateLocation')->name('update_location');
        Route::post('/arrive', 'arrive')->name('arrive');
        Route::post('/start', 'start')->name('start');
        Route::post('/complete-location', 'completeLocation')->name('complete_location');
        Route::post('/complete', 'complete')->name('complete');
    });

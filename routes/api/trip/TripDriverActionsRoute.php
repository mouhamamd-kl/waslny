<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripDriverActionsController;

Route::middleware(['auth:driver-api'])
    ->controller(TripDriverActionsController::class)
    ->prefix('trip/{trip}')
    ->name('trip.driver.')
    ->group(function () {
        Route::post('/accept', 'accept')->name('accept');
        Route::post('/location', 'updateLocation')->name('update_location');
        Route::post('/arrive', 'arrive')->name('arrive');
        Route::post('/start', 'start')->name('start');
        Route::post('/complete-location/{tripLocation}', 'completeLocation')->name('complete_location');
        Route::post('/complete', 'complete')->name('complete');
    });

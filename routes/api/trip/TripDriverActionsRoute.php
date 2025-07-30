<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripDriverActionsController;

Route::middleware(['auth:driver-api'])->group(function () {
    Route::post('/trip/{trip}/accept', [TripDriverActionsController::class, 'accept']);
    Route::post('/trip/{trip}/location', [TripDriverActionsController::class, 'updateLocation']);
    Route::post('/trip/{trip}/arrive', [TripDriverActionsController::class, 'arrive']);
    Route::post('/trip/{trip}/start', [TripDriverActionsController::class, 'start']);
});

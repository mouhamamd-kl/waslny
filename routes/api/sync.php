<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SyncController;

Route::prefix('sync')->group(function () {
    Route::middleware('auth:driver-api')->get('/driver', [SyncController::class, 'syncDriver']);
    Route::middleware('auth:rider-api')->get('/rider', [SyncController::class, 'syncRider']);
});

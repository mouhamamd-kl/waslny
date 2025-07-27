<?php

use App\Http\Controllers\Admin\DriverSuspensionController;
use App\Http\Controllers\Admin\RiderSuspensionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('drivers.suspensions', DriverSuspensionController::class);
Route::apiResource('riders.suspensions', RiderSuspensionController::class);

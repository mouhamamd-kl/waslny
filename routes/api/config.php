<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PusherConfigController;

Route::get('/rider/pusher/config', PusherConfigController::class)->middleware(['auth:rider-api'])->name('rider.pusher.config');
Route::get('/driver/pusher/config', PusherConfigController::class)->middleware(['auth:driver-api'])->name('driver.pusher.config');
Route::get('/admin/pusher/config', PusherConfigController::class)->middleware(['auth:admin-api'])->name('admin.pusher.config');

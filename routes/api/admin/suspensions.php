<?php

use App\Http\Controllers\Admin\DriverSuspensionController;
use App\Http\Controllers\Admin\RiderSuspensionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin-api'])->prefix('suspensions')->group(function () {
    Route::controller(DriverSuspensionController::class)
        ->prefix('drivers')
        ->name('suspensions.drivers.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{suspension}', 'show')->name('show');
            Route::put('/{suspension}', 'update')->name('update');
            Route::delete('/{suspension}', 'destroy')->name('destroy');
        });

    Route::controller(RiderSuspensionController::class)
        ->prefix('riders')
        ->name('suspensions.riders.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{suspension}', 'show')->name('show');
            Route::put('/{suspension}', 'update')->name('update');
            Route::delete('/{suspension}', 'destroy')->name('destroy');
        });
});

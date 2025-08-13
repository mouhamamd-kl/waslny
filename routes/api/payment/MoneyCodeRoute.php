<?php

use App\Http\Controllers\MoneyCodeController;
use Illuminate\Support\Facades\Route;

// Rider routes
Route::middleware(['auth:rider-api'])
    ->controller(MoneyCodeController::class)
    ->prefix('money-codes')
    // ->name('money-codes.')
    ->name('rider.money-codes.')
    ->group(function () {
        Route::post('/redeem', 'redeem')->name('redeem');
    });

// Admin management routes
Route::middleware(['auth:admin-api'])
    ->controller(MoneyCodeController::class)
    ->prefix('admin/money-codes')
    ->name('admin.money-codes.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{moneyCode}', 'show')->name('show');
        Route::delete('/{moneyCode}', 'destroy')->name('destroy');
    });

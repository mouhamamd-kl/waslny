<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin-api'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/header-stats', [DashboardController::class, 'getHeaderStats'])->name('header-stats');
        Route::get('/chart-stats', [DashboardController::class, 'getChartStats'])->name('chart-stats');
    });

<?php
use App\Http\Controllers\CarModelController;
use Illuminate\Support\Facades\Route;


// Public routes (accessible by anyone)
Route::controller(CarModelController::class)
    ->prefix('car-models')
    ->name('car-models.')  // Corrected name prefix
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'search')->name('search');
        Route::get('/{car_model}', 'show')->name('show');
    });

// Admin-protected routes
Route::middleware(['auth:admin-api'])->controller(CarModelController::class)
    ->prefix('car-models')
    ->name('car-models.')
    ->group(function () {
        Route::post('/', 'store')->name('store');

        Route::prefix('{car_model}')->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

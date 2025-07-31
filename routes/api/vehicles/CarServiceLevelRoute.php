<?php
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin-api'])->controller(CountryController::class)
    ->prefix('service-leveles')
    ->name('service-level.') // Optional name prefix
    ->group(function () {
        // Top-level routes
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/search', 'search')->name('search');

        // Routes requiring car_manufacturer parameter
        Route::prefix('{car_service_level}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/deactivate', 'deActivate')->name('deactivate');
        });
    });

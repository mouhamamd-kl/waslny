<?php
// routes/api.php
use App\Http\Controllers\RiderSavedLocationController;
use Illuminate\Support\Facades\Route;

// Rider-specific routes (protected by rider-api guard)
Route::middleware('auth:rider-api')->group(function () {
    Route::controller(RiderSavedLocationController::class)
        ->prefix('rider-saved-locations')
        // ->name('rider-saved-locations.')
        ->name('rider.rider-saved-locations.')
        ->group(function () {
            // Collection routes
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');

            // Resource routes
            Route::prefix('{rider_saved_location}')->group(function () {
                Route::get('/', 'show')->name('show');
                Route::put('/', 'update')->name('update');
                Route::delete('/', 'destroy')->name('destroy');
            });
        });
});

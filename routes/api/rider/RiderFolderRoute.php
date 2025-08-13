<?php
// routes/api.php
use App\Http\Controllers\RiderFolderController;
use Illuminate\Support\Facades\Route;

// Rider-specific routes (protected by rider-api guard)
Route::middleware(['auth:rider-api','rider.profile.completed'])->group(function () {
    Route::controller(RiderFolderController::class)
        ->prefix('rider-folders')
        // ->name('rider-folders.')
        ->name('rider.rider-folders.')
        ->group(function () {
            // Collection routes
            Route::get('/', 'index')->name('index');
            Route::post('/search', 'search')->name('search');
            Route::post('/', 'store')->name('store');

            // Resource routes
            Route::prefix('{rider_folder}')->group(function () {
                Route::get('/', 'show')->name('show');
                Route::put('/', 'update')->name('update');
                Route::delete('/', 'destroy')->name('destroy');
            });
        });
});

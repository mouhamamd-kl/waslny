<?php

use Illuminate\Support\Facades\Route;

Route::get('/telescope/clear-entries', function () {
    \Illuminate\Support\Facades\Artisan::call('telescope:clear');
    return response()->json(['message' => 'Telescope entries cleared.']);
})->middleware('telescope.clear.auth')->name('no-export.telescope.clear');

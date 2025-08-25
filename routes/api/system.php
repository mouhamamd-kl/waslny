<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::post('/process-scheduled-trips', function () {
    try {
        Artisan::call('trips:process-scheduled');
        return response()->json([
            'message' => 'Scheduled trips processing command executed successfully.',
            'output' => Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while executing the command.',
            'error' => $e->getMessage()
        ], 500);
    }
})->name('no-export.system.process-scheduled-trips');

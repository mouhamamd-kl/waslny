<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemConfigController;

Route::apiResource('system-configs', SystemConfigController::class)->only(['index', 'update']);

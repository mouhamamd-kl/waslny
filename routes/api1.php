<?php

use Clickbar\Magellan\Database\PostgisFunctions\ST;

use App\Events\TestEvent;
use App\Events\TestNotification;
use App\Helpers\ApiResponse;
use App\Services\FileServiceFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FileController;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use App\Models\Rider;
use App\Events\TestQueuedNotification;
use App\Http\Controllers\PusherWebhookController;
use App\Http\Requests\RiderSavedLocation\RiderSavedLocationRequest;
use App\Http\Requests\Trip\TripRequest;
use App\Models\Trip;
use App\Notifications\TestFirebaseNotification;
use Illuminate\Support\Facades\Redis;

require __DIR__ . '/api/config.php';
// require __DIR__ . '/api/driver/DriverStatusRoute.php';

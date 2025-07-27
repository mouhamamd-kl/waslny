<?php

use App\Enums\TripStatusEnum;
use App\Events\TestEvent;
use App\Events\TestNotification;
use App\Events\TripAvailableForDriver;
use App\Helpers\ApiResponse;
use App\Services\FileServiceFactory;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FileController;
use App\Http\Requests\RiderSavedLocationRequest;
use App\Http\Requests\TripRequest;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripDriverNotification;
use App\Services\TripService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::get('/test', function (Request $request) {
//     // Storing a value in Redis
//     Redis::set('القائد', 'يحيى السنوار');

//     // Retrieving a value from Redis
//     $value = Redis::get('القائد');
//     return response()->json(['القائد' => $value]);
// });


Route::get('/trigger-event', function () {
    event(new TestNotification([
        'قائد الطوفان' => 'القائد يحيى السنوار'
    ]));
    return response()->json(['message' => 'Event triggered']);
});



Route::post('/trigger-event2', function (Request $request) {
    event(new TestEvent([
        'driver_id' => $request->driver_id,
        'lat' => $request->lat,
        'lng' => $request->lng
    ]));

    return response()->json([
        'driver_id' => $request->driver_id,
        'lat' => $request->lat,
        'lng' => $request->lng
    ]);
});




// Public upload endpoint with rate limiting
Route::post('/files/exists', [FileController::class, 'exists']);



Route::post('/files', [FileController::class, 'upload']); // 10 requests per

Route::get('/testfile', function () {
    //delete

    // return Storage::disk(env('AWS_DISK_PUBLIC'))->delete('Untitled//alqasam.jpg');


    //exists
    // $filePath = 'Untitled//alqasam.jpg';

    // $exists = Storage::disk('supabase')->exists($filePath);



    // return response()->json([
    //     'exists' => $exists,
    //     'path' => $filePath
    // ]);


    // try {
    //     $exists = Storage::disk('supabase')->exists($filePath);

    //     return response()->json([
    //         'exists' => $exists,
    //         'path' => $filePath
    //     ]);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'error' => 'Error checking file existence',
    //         'message' => $e->getMessage()
    //     ], 500);
    // }
});

// Route::post('/files2', function (Request $request) {
//     //public
//     // $filepath = Storage::disk('supabase')->put('', $request->file('file'));
//     // $url = Storage::disk('supabase')->url($filepath);


//     //private
//     $filepath = Storage::disk('supabase_test')->put('', $request->file('file'));
//     $signedUrl = Storage::disk('supabase_test')
//         ->temporaryUrl(
//             $filepath,
//             now()->addMinutes(10)
//         );
//     return $signedUrl;
// }); // 10 requests per 

// Route::post('/fileso', function(Request $request){
//     Storage::disk('s3')->put('test.txt', 'Hello, Supabase!');
// }); // 10 requests per minute


// Protected delete endpoint
Route::delete('/files', [FileController::class, 'deleteFile']);


Route::post('/testFile', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $path = $service->uploadPublic($request->file('file'));

    // Generate temporary URL for private access
    return $path;
});

Route::get('/testFile/getpath', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->getFilePath($request->input('url'));

    // Generate temporary URL for private access
    return $filePath;
});

Route::get('/testFile/geturl', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->getUrl($request->input('filepath'));

    // Generate temporary URL for private access
    return $filePath;
});

Route::get('/testFile/exsist', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->exists($request->input('filepath'));

    // Generate temporary URL for private access
    return $filePath;
});

Route::delete('/testFile', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $status = $service->delete($request->input('filepath'));

    // Generate temporary URL for private access
    return $status;
});

Route::post('/testdriversaved', function (RiderSavedLocationRequest $request) {

    $point = $request->input('location');

    return response()->json([
        'success' => true,
        'coordinates' => [
            'longitude' => $point->getLongitude(), // or getLongitude() if using geodetic
            'latitude' => $point->getLatitude(),    // or getLatitude() if using geodetic
        ]
    ]);
});


Route::post('/test', function (TripRequest $request) {

    $data = $request->validate();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
});



Route::post('/test-functions', function (Request $request) {
    // Trigger the event
    fireAndForgetRequest(config('functions.driver_search_url'), [
        'headers' => [
            'X-Driver-Search-Secret' => config('functions.driver_search_secret'),
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode(['message' => 'hello']),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Event triggered successfully',
    ]);
});


Route::post('/trip/find-driver', function (Request $request) {
    // Authorization
    if ($request->header('X-Driver-Search-Secret') !== env('DRIVER_SEARCH_SECRET')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $tripId = $request->input('trip_id');
    $trip = Trip::with('rider')->findOrFail($tripId);

    // If driver already found, stop searching
    if ($trip->driver_id) {
        return response()->json([
            'status' => 'completed',
            'result' => 'driver_found',
            'driver_id' => $trip->driver_id
        ]);
    }

    // Initialize search if first attempt
    if (!$trip->search_started_at) {
        $trip->update([
            'search_started_at' => now(),
            'search_expires_at' => now()->addMinutes(5)
        ]);
    }

    // Find available drivers within radius
    $drivers = findNearbyDrivers($trip);

    // Send notifications to found drivers
    foreach ($drivers as $driver) {
        notifyDriver($trip, $driver);
    }

    // Check for accepted drivers
    if ($acceptedDriver = checkForAcceptedDriver($trip)) {
        $trip->update(['driver_id' => $acceptedDriver->id]);
        return response()->json([
            'status' => 'completed',
            'result' => 'driver_accepted',
            'driver_id' => $acceptedDriver->id
        ]);
    }

    // Expand search radius if no drivers found
    if ($drivers->isEmpty()) {
        $trip->increment('driver_search_radius', 1000); // Expand by 1km
    }

    // Check if search should continue
    if ($trip->search_expires_at->isPast()) {
        $trip->update(['trip_status_id' => TripStatusEnum::SystemCancelled->value]);
        return response()->json([
            'status' => 'completed',
            'result' => 'search_expired'
        ]);
    }

    // Queue next search attempt
    queueNextSearch($trip);

    return response()->json([
        'status' => 'searching',
        'radius' => $trip->driver_search_radius,
        'notified_drivers' => $drivers->pluck('id')
    ]);
});

// Helper methods
function findNearbyDrivers(Trip $trip)
{
    return Driver::where('driver_status_id', 'available') // Available status
        ->whereDoesntHave('notifications', function ($query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })
        ->whereRaw(
            "ST_DWithin(location, ?, ?)",
            [
                $trip->current_location,
                $trip->driver_search_radius
            ]
        )
        ->orderByRaw("rating DESC, ST_Distance(location, ?)", [$trip->current_location])
        ->limit(5)
        ->get();
}

function notifyDriver(Trip $trip, Driver $driver)
{
    // Initialize the TripService
    $tripService = app(TripService::class); // or inject it in your constructor

    // Get the trip with all relations using the service
    $tripWithRelations = $tripService->findTripById($trip->id);

    // Convert the trip to an array (or use a Resource if you have one)
    $tripData = $tripWithRelations->toArray();

    // Send push notification to driver
    $driver->notify(new TripAvailableForDriver($tripData, $driver->id));

    // Record notification
    TripDriverNotification::create([
        'trip_id' => $trip->id,
        'driver_id' => $driver->id,
        'sent_at' => now(),
    ]);
}

function checkForAcceptedDriver(Trip $trip)
{
    return TripDriverNotification::where('trip_id', $trip->id)
        ->where('status', 'accepted')
        ->first()
        ?->driver;
}

function queueNextSearch(Trip $trip)
{
    // Queue next search attempt in 15 seconds
    dispatch(function () use ($trip) {
        Http::withHeaders([
            'X-Driver-Search-Secret' => env('DRIVER_SEARCH_SECRET')
        ])->post(config('app.url') . '/trip/find-driver', [
            'trip_id' => $trip->id
        ]);
    })->delay(now()->addSeconds(15));
}


Route::post('/test-htpp', function (Request $request) {
    // 1. Authorization
    if ($request->header('X-Driver-Search-Secret') !== env('DRIVER_SEARCH_SECRET')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // 2. Get parameters with defaults
    $start = $request->input('start', 0);
    $totalIterations = $request->input('total', 200);
    $chunkSize = $request->input('chunk', 5); // Iterations per execution
    $startTime = $request->input(microtime(true));

    // 3. Calculate remaining time (4.5 minutes buffer)
    $maxDuration = 3.0 * 60; // 4.5 minutes in seconds
    $remainingIterations = $totalIterations - $start;

    // 4. Process current chunk
    $processed = 0;
    for ($i = $start; $i < $start + min($chunkSize, $remainingIterations); $i++) {
        // Your actual task
        event(new TestNotification([
            'message' => "Iteration $i of $totalIterations",
            'chunk' => ceil(($i + 1) / $chunkSize)
        ]));

        // Simulate work (60 seconds per iteration)
        sleep(60);
        $processed++;

        // Check time after each iteration
        $currentElapsed = microtime(true) - $startTime;
        if ($currentElapsed > $maxDuration) {
            break;
        }
    }

    // 5. Calculate next chunk
    $nextStart = $start + $processed;
    $completed = $nextStart >= $totalIterations;

    // 6. Trigger next chunk if not completed
    if (!$completed) {
        fireAndForgetRequest(config('functions.driver_search_url'), [
            'headers' => [
                'X-Driver-Search-Secret' => env('DRIVER_SEARCH_SECRET'),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'start' => $nextStart,
                'total' => $totalIterations,
                'chunk' => $chunkSize,
            ])
        ]);

        Log::info("Chunk queued", [
            'completed' => $nextStart,
            'total' => $totalIterations,
            'next_chunk' => $nextStart
        ]);
    }

    // 7. Return status
    return response()->json([
        'status' => $completed ? 'completed' : 'partial',
        'processed' => $processed,
        'total_processed' => $nextStart,
        'remaining' => $totalIterations - $nextStart,
        'next_start' => $completed ? null : $nextStart,
        'chunk_size' => $chunkSize
    ]);
});

Route::post('/add_website_photo', function (Request $request) {
    $assetService = FileServiceFactory::makeForSystemFiles();
    $url =  $assetService->uploadPublic($request->file('file'));
    return ApiResponse::sendResponseSuccess($url);
});

require __DIR__ . '/api/auth/AdminAuth.php';
require __DIR__ . '/api/auth/DriverAuth.php';
require __DIR__ . '/api/auth/RiderAuth.php';
require __DIR__ . '/api/coupon/CouponRoute.php';
require __DIR__ . '/api/driver/DriverRoute.php';
require __DIR__ . '/api/payment/PaymentMethodRoute.php';

require __DIR__ . '/api/rider/RiderRoute.php';
require __DIR__ . '/api/rider/RiderFolderRoute.php';
require __DIR__ . '/api/rider/RiderSavedLocationRoute.php';

require __DIR__ . '/api/trip/TripRoute.php';
require __DIR__ . '/api/trip/TripStatusRoute.php';
require __DIR__ . '/api/trip/TripTimeTypeRoute.php';
require __DIR__ . '/api/trip/TripTypeRoute.php';

require __DIR__ . '/api/veichles/CarManufactureRoute.php';
require __DIR__ . '/api/veichles/CarModelRoute.php';
require __DIR__ . '/api/veichles/CarServiceLevelRoute.php';
require __DIR__ . '/api/veichles/CountryRoute.php';
require __DIR__ . '/api/veichles/PricingRoute.php';








Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin-api']], function () {
    require __DIR__ . '/api/admin/suspensions.php';
});

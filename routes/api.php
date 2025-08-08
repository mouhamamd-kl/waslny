<?php

use App\Constants\DiskNames;
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
use App\Models\CarModel;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripDriverNotification;
use App\Services\TripService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Rider;
use App\Events\TestQueuedNotification;
use App\Notifications\TestFirebaseNotification;


Route::get('/test-queued-job', function () {
    for ($i = 0; $i < 10; $i++) {
        event(new TestQueuedNotification('This is a test queued job.'));
    }
});


Route::get('/test-firebase/{type}/{id}', function ($type, $id) {
    if ($type === 'driver') {
        $user = Driver::find($id);
    } elseif ($type === 'rider') {
        $user = Rider::find($id);
    } else {
        return response()->json(['error' => 'Invalid user type'], 400);
    }

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $user->notify(new TestFirebaseNotification());

    return response()->json(['message' => 'Test notification sent.']);
})->name('no-export');

// Route::get('/test', function (Request $request) {
//     return CarModel::where('name','Civic')->get();
// });


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
})->name('no-export.test.trigger_event');



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
})->name('no-export.test.trigger_event2');




// Public upload endpoint with rate limiting
Route::post('/files/exists', [FileController::class, 'exists'])->name('no-export.test.file_exists');



Route::post('/files', [FileController::class, 'upload'])->name('no-export.test.file_upload'); // 10 requests per

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
})->name('no-export.test.test_file');

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
Route::delete('/files', [FileController::class, 'deleteFile'])->name('no-export.test.file_delete');


Route::post('/testFile', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $path = $service->uploadPublic($request->file('file'));

    // Generate temporary URL for private access
    return $path;
})->name('no-export.test.test_file_upload');

Route::get('/testFile/getpath', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->getFilePath($request->input('url'));

    // Generate temporary URL for private access
    return $filePath;
})->name('no-export.test.test_file_get_path');

Route::get('/testFile/geturl', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->getUrl($request->input('filepath'));

    // Generate temporary URL for private access
    return $filePath;
})->name('no-export.test.test_file_get_url');

Route::get('/testFile/exsist', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $filePath = $service->exists($request->input('filepath'));

    // Generate temporary URL for private access
    return $filePath;
})->name('no-export.test.test_file_exists');

Route::delete('/testFile', function (Request $request) {
    /** @var BaseFileService $service */
    $service = FileServiceFactory::makeForDriverLicense();
    $status = $service->delete($request->input('filepath'));

    // Generate temporary URL for private access
    return $status;
})->name('no-export.test.test_file_delete');

Route::post('/testdriversaved', function (RiderSavedLocationRequest $request) {

    $point = $request->input('location');

    return response()->json([
        'success' => true,
        'coordinates' => [
            'longitude' => $point->getLongitude(), // or getLongitude() if using geodetic
            'latitude' => $point->getLatitude(),    // or getLatitude() if using geodetic
        ]
    ]);
})->name('no-export.test.driver_saved_location');

Route::get('/testo', function (Request $request) {
    return response()->json(['message' => 'hello']);
})->name('no-export.test.simple_test');
Route::post('/test', function (TripRequest $request) {

    $data = $request->validate();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
})->name('no-export.test.trip_request');



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
})->name('no-export.test.test_functions');




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
})->name('no-export.test.http_long_polling');

Route::post('/add_website_photo', function (Request $request) {
    $assetService = FileServiceFactory::makeForSystemFiles();
    $url =  $assetService->uploadPublic($request->file('file'));
    return ApiResponse::sendResponseSuccess($url);
})->name('no-export.test.add_website_photo');

require __DIR__ . '/api/auth/AdminAuth.php';
require __DIR__ . '/api/auth/DriverAuth.php';
require __DIR__ . '/api/auth/RiderAuth.php';
require __DIR__ . '/api/coupon/CouponRoute.php';
require __DIR__ . '/api/driver/DriverRoute.php';
require __DIR__ . '/api/payment/PaymentMethodRoute.php';
require __DIR__ . '/api/payment/MoneyCodeRoute.php';

require __DIR__ . '/api/rider/RiderRoute.php';
require __DIR__ . '/api/rider/RiderFolderRoute.php';
require __DIR__ . '/api/rider/RiderSavedLocationRoute.php';

require __DIR__ . '/api/trip/TripRoute.php';
require __DIR__ . '/api/trip/TripStatusRoute.php';
require __DIR__ . '/api/trip/TripTimeTypeRoute.php';
require __DIR__ . '/api/trip/TripTypeRoute.php';

require __DIR__ . '/api/vehicles/CarManufactureRoute.php';
require __DIR__ . '/api/vehicles/CarModelRoute.php';
require __DIR__ . '/api/vehicles/CarServiceLevelRoute.php';
require __DIR__ . '/api/vehicles/CountryRoute.php';
require __DIR__ . '/api/vehicles/PricingRoute.php';

require __DIR__ . '/api/admin/suspensions.php';

require __DIR__ . '/api/trip/TripDriverActionsRoute.php';

Route::get('/test-log', function () {
    Log::info('This is a test log message from Laravel.');
    return 'Log message sent!';
})->name('no-export.test.log');

// Telegram Bot Integration
Route::post('/telegram/webhook', [App\Http\Controllers\Api\TelegramController::class, 'webhook'])->name('no-export.test.add_website_photo');

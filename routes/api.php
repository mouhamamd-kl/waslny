<?php

use App\Events\TestEvent;
use App\Events\TestNotification;
use App\Services\FileServiceFactory;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Storage;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function (Request $request) {
    // Storing a value in Redis
    Redis::set('القائد', 'يحيى السنوار');

    // Retrieving a value from Redis
    $value = Redis::get('القائد');
    return response()->json(['القائد' => $value]);
});


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

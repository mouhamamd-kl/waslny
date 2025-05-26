<?php

use App\Events\TestEvent;
use App\Events\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

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



use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Storage;

// Public upload endpoint with rate limiting
Route::post('/files/exists', [FileController::class, 'exists']);


Route::post('/files', [FileController::class, 'upload']); // 10 requests per 
// Route::post('/files', function(Request $request){
//     Storage::disk('s3')->put('test.txt', 'Hello, Supabase!');
// }); // 10 requests per minute


// Protected delete endpoint
Route::delete('/files', [FileController::class, 'delete']);

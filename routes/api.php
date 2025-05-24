<?php

use App\Events\TestEvent;
use App\Events\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/test', function (Request $request) {
    return 'hello';
});
Route::get('/trigger-event', function () {
    event(new TestNotification([
        'driver_id' => 1,
        'lat' => 2,
        'lng' => 2
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

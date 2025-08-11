<?php

use App\Enums\channels\BroadCastChannelEnum;
use App\Enums\channels\NotificationChannel;
use App\Models\Driver;
use App\Models\Rider;
use App\Models\Trip;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel(BroadCastChannelEnum::RIDER->pattern(), function (Rider $rider, $riderId) {
//     return (int) $rider->id === (int) $riderId && $rider->isSuspended() === false;
// }, ['guards' => ['rider-api']]);

// Broadcast::channel(BroadCastChannelEnum::DRIVER->pattern(), function (Driver $driver, $driverId) {
//     return  (int) $driver->id === (int) $driverId && $driver->isSuspended() === false;
// }, ['guards' => ['driver-api']]);

// Broadcast::channel(BroadCastChannelEnum::DRIVERS_ONLINE->pattern(), function (Driver $driver) {
//     return $driver->id;
// }, ['guards' => ['driver-api']]);

// Broadcast::channel(BroadCastChannelEnum::TRIP->pattern(), function ($user, $tripId) {
//     return Trip::where('id', $tripId)
//         ->where(function ($query) use ($user) {
//             $query->where('user_id', $user->id)
//                 ->orWhere('driver_id', $user->id);
//         })->exists();
// }, ['guards' => ['rider-api', 'driver-api']]);

Broadcast::channel(BroadCastChannelEnum::RIDER->pattern(), function (Rider $rider, $riderId) {
    return (int) $rider->id === (int) $riderId && $rider->isSuspended() === false;
});

Broadcast::channel(BroadCastChannelEnum::DRIVER->pattern(), function (Driver $driver, $driverId) {
    return  (int) $driver->id === (int) $driverId && $driver->isSuspended() === false;
});

Broadcast::channel(BroadCastChannelEnum::DRIVERS_ONLINE->pattern(), function (Driver $driver) {
    return $driver->id;
});

Broadcast::channel(BroadCastChannelEnum::TRIP->pattern(), function ($user, $tripId) {
    return Trip::where('id', $tripId)
        ->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('driver_id', $user->id);
        })->exists();
});

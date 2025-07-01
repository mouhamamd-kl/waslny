<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('rider.{riderId}', function ($rider, $riderId) {
    return (int) $rider->id === (int) $riderId;
});

Broadcast::channel('driver.{driverId}', function ($driver, $driverId) {
    return  (int) $driver->id === (int) $driverId;
});

Broadcast::channel('online-drivers', function ($driver) {
    return $driver();
});

Broadcast::channel('trip.{tripId}', function ($user, $tripId) {
    return Trip::where('id', $tripId)
        ->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('driver_id', $user->id);
        })->exists();
});

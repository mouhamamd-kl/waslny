<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;
use App\Enums\channels\BroadCastChannelEnum;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function syncDriver(Request $request)
    {
        $driver = auth('driver-api')->user();
        $channels = [];

        // Add personal and global driver channels
        $channels[] = BroadCastChannelEnum::DRIVER->bind($driver->id);
        $channels[] = BroadCastChannelEnum::DRIVERS_ONLINE->value;

        // Add trip channels using the correct scope
        $activeTrips = Trip::forDriver($driver->id)->activeOrScheduled()->get();
        foreach ($activeTrips as $trip) {
            $channels[] = BroadCastChannelEnum::TRIP->bind($trip->id);
        }

        return ChannelResource::collection($channels);
    }

    public function syncRider(Request $request)
    {
        $rider = auth('rider-api')->user();
        $channels = [];

        // Add personal rider channel
        $channels[] = BroadCastChannelEnum::RIDER->bind($rider->id);

        // Add trip channels using the correct scope
        $activeTrips = Trip::forRider($rider->id)->activeOrScheduled()->get();
        foreach ($activeTrips as $trip) {
            $channels[] = BroadCastChannelEnum::TRIP->bind($trip->id);
        }

        return ChannelResource::collection($channels);
    }
}

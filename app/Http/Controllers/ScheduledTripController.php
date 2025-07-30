<?php

namespace App\Http\Controllers;

use App\Enums\TripStatusEnum;
use App\Enums\TripTimeTypeEnum;
use App\Events\TripTimeIsNow;
use App\Events\TripTimeNear;
use App\Models\Trip;
use App\Models\TripStatus;
use App\Models\TripTimeType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduledTripController extends Controller
{
    public function checkScheduledTrips(Request $request)
    {
        // 1. Authenticate the request
        if ($request->header('X-Vercel-Cron-Secret') !== env('VERCEL_CRON_SECRET')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Fetch scheduled trips
        $scheduledTripStatus = TripStatus::where('name', TripStatusEnum::Pending->value)->first();
        $pendingTripTimeType = TripTimeType::where('name', TripTimeTypeEnum::SCHEDULED->value)->first();
        $scheduledTrips = Trip::where('trip_time_type_id', $pendingTripTimeType->id)
            ->where('trip_status_id', $scheduledTripStatus->id)
            ->get();

        foreach ($scheduledTrips as $trip) {
            $now = Carbon::now();
            $requestedTime = Carbon::parse($trip->requested_time);

            // 3. Check if trip time is near
            if ($now->diffInMinutes($requestedTime) <= 15 && $now < $requestedTime) {
                event(new TripTimeNear($trip));
            }

            // 4. Check if it's time to start the trip
            if ($now >= $requestedTime) {
                event(new TripTimeIsNow($trip));
            }
        }

        return response()->json(['status' => 'success']);
    }
}

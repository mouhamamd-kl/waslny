<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\DriverAssigned;
use App\Helpers\ApiResponse;
use App\Events\DriverLocationUpdated;
use App\Events\DriverArrived;
use App\Events\TripStarted;
use App\Enums\TripStatusEnum;
use App\Models\Trip;
use Exception;

class TripDriverActionsController extends Controller
{
    public function accept(Request $request, Trip $trip)
    {
        try {
            $driver = auth('driver-api')->user();
            $trip->assignDriver($driver);

            event(new DriverAssigned($trip, $driver));

            return ApiResponse::sendResponseSuccess(
                [],
                'Trip accepted successfully.',
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function updateLocation(Request $request, Trip $trip)
    {
        try {
            $location = $request->validate([
                'location' => 'required',
            ]);

            event(new DriverLocationUpdated($trip->id, $location));

            return ApiResponse::sendResponseSuccess([], 'Location updated successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function arrive(Request $request, Trip $trip)
    {
        try {
            $trip->transitionTo(TripStatusEnum::DriverArrived);

            event(new DriverArrived($trip));

            return ApiResponse::sendResponseSuccess([], 'Driver arrived successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function start(Request $request, Trip $trip)
    {
        try {
            $trip->startTrip();

            event(new TripStarted($trip));

            return ApiResponse::sendResponseSuccess(
                [],
                'Trip started successfully.',
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }
}

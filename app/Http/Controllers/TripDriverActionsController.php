<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\DriverAssigned;
use App\Helpers\ApiResponse;
use App\Events\DriverLocationUpdated;
use App\Events\DriverArrived;
use App\Events\TripStarted;
use App\Events\TripLocationCompleted;
use App\Events\TripCompleted;
use App\Enums\TripStatusEnum;
use App\Models\Trip;
use App\Models\TripLocation;
use App\Services\TripLocationService;
use App\Services\TripService;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Exception;

class TripDriverActionsController extends Controller
{
    protected $tripService;
    protected $tripLocationService;

    public function __construct(TripService $tripService, TripLocationService $tripLocationService)
    {
        $this->tripService = $tripService;
        $this->tripLocationService = $tripLocationService;
    }

    public function accept(Request $request, Trip $trip)
    {
        try {
            if ($trip->driver_id) {
                return ApiResponse::sendResponseError('Trip has already been accepted.');
            }

            if ($trip->status->name !== TripStatusEnum::Searching->value) {
                return ApiResponse::sendResponseError('Trip is no longer available.');
            }

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

    public function completeLocation(Request $request, Trip $trip)
    {
        if ($trip->isCompleted()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_location.error.trip_completed', 'Cannot edit a completed trip')
            );
        }

        $requestData = $request->validate([
            'current_location' => [new GeometryGeojsonRule([Point::class])],
            'location_id' => 'nullable|exists:trip_locations,id,trip_id,' . $trip->id
        ]);

        $tripLocation = null;
        if ($request->location_id) {
            $tripLocation = $this->tripLocationService->findById($requestData['location_id']);
        } else {
            $tripLocation = $this->tripLocationService->getNextPendingTripLocation($trip->id);
        }

        if (!$tripLocation) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip Location not found'), 404);
        }

        if ($tripLocation->isCompleted()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_location.error.already_completed', 'Trip Location Already Completed')
            );
        }

        if (!$tripLocation->completeTripLocation()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_location.error.completion_failed', 'Trip Location Completion failed')
            );
        }

        event(new TripLocationCompleted($trip, $tripLocation));

        return ApiResponse::sendResponseSuccess([], 'Location completed successfully.', 200);
    }

    public function complete(Request $request, Trip $trip)
    {
        try {
            $trip->completeTrip();

            event(new TripCompleted($trip));

            return ApiResponse::sendResponseSuccess([], 'Trip completed successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }
}

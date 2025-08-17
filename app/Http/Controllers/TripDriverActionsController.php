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
use App\Events\TripDriverLocationUpdated;
use App\Http\Requests\Trip\DriverTripUpdateLocationRequest;
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

    public function accept(Request $request, $id)
    {
        try {
            $trip = $this->tripService->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }

            if ($trip->driver_id) {
                return ApiResponse::sendResponseError(trans_fallback('messages.driver.error.already_accepted', 'Trip has already been accepted.'));
            }

            if (!$trip->hasStatus(TripStatusEnum::Searching)) {
                return ApiResponse::sendResponseError(trans_fallback('messages.driver.error.no_longer_available', 'Trip is no longer available.'));
            }

            $driver = auth('driver-api')->user();
            $trip->assignDriver($driver);

            $trip->load($this->tripService->getRelations());
            event(new DriverAssigned($trip, $driver));

            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.trip.accepted', 'Trip accepted successfully.'),
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function updateLocation(DriverTripUpdateLocationRequest $request, $id)
    {
        try {
            $trip = $this->tripService->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
            if (!$trip->hasStatus(TripStatusEnum::DriverEnRoute)) {
                $trip->transitionTo(TripStatusEnum::DriverEnRoute);
            }

            $location = $request->validated();

            event(new TripDriverLocationUpdated($trip->id, $location));

            return ApiResponse::sendResponseSuccess([], trans_fallback('messages.driver.location_updated', 'Location updated successfully.'), 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function arrive(Request $request, $id)
    {
        try {
            $trip = $this->tripService->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
            if ($trip->hasStatus(TripStatusEnum::DriverArrived) && $trip->pickup_location->isCompleted()) {
                return ApiResponse::sendResponseError(trans_fallback('messages.trip.error.already_arrived', 'Driver has already arrived.'), 422);
            }
            $trip->pickup_location->completeTripLocation();
            $trip->transitionTo(TripStatusEnum::DriverArrived);
            $trip->load($this->tripService->getRelations());
            event(new DriverArrived(auth('driver-api')->user(), $trip));

            return ApiResponse::sendResponseSuccess([], trans_fallback('messages.trip.driver_arrived', 'Driver arrived at pickup point'), 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function start(Request $request, $id)
    {
        try {
            $trip = $this->tripService->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
            if ($trip->hasStatus(TripStatusEnum::OnGoing)) {
                return ApiResponse::sendResponseError(trans_fallback('messages.trip.error.already_arrived', 'Driver has already arrived.'), 422);
            }
            $trip->startTrip();

            $trip->load($this->tripService->getRelations());
            event(new TripStarted($trip));

            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.trip.started', 'Trip started successfully.'),
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function completeLocation(Request $request, $id)
    {
        $trip = $this->tripService->findById($id);
        if (!$trip) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
        }

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

        $trip->load($this->tripService->getRelations());
        event(new TripLocationCompleted($trip, $tripLocation));

        return ApiResponse::sendResponseSuccess([], trans_fallback('messages.trip.location_completed', 'Location completed successfully.'), 200);
    }

    public function complete(Request $request, $id)
    {
        try {
            $trip = $this->tripService->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
            if ($trip->hasStatus(TripStatusEnum::Completed)) {
                return ApiResponse::sendResponseError(trans_fallback('messages.trip.error.already_arrived', 'Driver has already arrived.'), 422);
            }

            $trip->dropoff_location->completeTripLocation();
            $trip->completeTrip();
            $trip->load($this->tripService->getRelations());
            event(new TripCompleted($trip));

            return ApiResponse::sendResponseSuccess([], trans_fallback('messages.trip.completed', 'Trip completed successfully.'), 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }
}

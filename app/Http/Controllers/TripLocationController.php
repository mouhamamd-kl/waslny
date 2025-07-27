<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\TripLocation;
use App\Services\TripLocationService;
use App\Services\TripService;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Http\Request;

class TripLocationController extends Controller
{
    protected $tripLocationService;
    protected $tripService;

    public function __construct(TripService $tripService, TripLocationService $tripLocationService)
    {
        $this->tripLocationService = $tripLocationService;
        $this->tripService = $tripService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(TripLocation $tripLocation)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TripLocation $tripLocation)
    {
        //
    }

    // Unified endpoint that accepts either method
    public function completeLocation($tripId, Request $request)
    {
        /** @var Trip $trip */ // Add PHPDoc type hint
        $trip = $this->tripService->findById($tripId);
        if (!$trip || $trip->isCompleted()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_location.error.trip_completed', 'Cannot edit a completed trip')
            );
        }

        $requestData = $request->validate([
            'current_location' => [new GeometryGeojsonRule([Point::class])],
            'location_id' => 'nullable|exists:trip_locations,id,trip_id,' . $trip->id
        ]);

        /** @var TripLocation $tripLocation */ // Add PHPDoc type hint
        $tripLocation;
        // Method 1: Explicit location ID
        if ($request->location_id) {
            /** @var TripLocation $tripLocation */ // Add PHPDoc type hint
            $tripLocation = $this->tripLocationService->findById($requestData['location_id']);
        } else {
            $tripLocation = $this->tripLocationService->getNextPendingTripLocation($tripId);
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

        event();

        
        // ... verification and completion logic ...
    }
}

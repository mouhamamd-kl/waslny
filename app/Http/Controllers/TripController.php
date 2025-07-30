<?php

namespace App\Http\Controllers;

use App\Enums\TripStatusEnum;
use App\Events\TripCancelledByDriver;
use App\Events\TripCancelledByRider;
use App\Events\TripCompleted;
use App\Helpers\ApiResponse;
use App\Http\Requests\TripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Models\TripLocation;
use App\Models\TripStatus;
use App\Services\TripService;
use App\Services\TripStatusService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    protected $trip_service;
    protected $trip_status_service;
    public function __construct(TripService $trip_service, TripStatusService $trip_status_service)
    {
        $this->trip_service = $trip_service;
        $this->trip_status_service = $trip_status_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $trips = $this->trip_service->searchTrips(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $trips,
                TripResource::class,
                trans_fallback('messages.trip.list', 'Trip retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function driverIndex(Request $request)
    {
        try {
            $filters['driver_id'] = auth('driver-api')->user()->id;
            $trips = $this->trip_service->searchTrips(
                $filters,
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $trips,
                TripResource::class,
                trans_fallback('messages.trip.list', 'Trip retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function riderIndex(Request $request)
    {
        try {
            $filters['rider_id'] = auth('rider-api')->user()->id;
            $trips = $this->trip_service->searchTrips(
                $filters,
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $trips,
                TripResource::class,
                trans_fallback('messages.trip.list', 'Trip retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        try {
            $filters = $request->only([
                'name',
                'is_active',
            ]);
            $trips = $this->trip_service->searchTrips(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $trips,
                TripResource::class, // Add your resource class
                trans_fallback('messages.trip.list', 'Trip retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }


    public function completeTrip(Trip $trip)
    {
        try {
            $trip->completeTrip();

            event(new TripCompleted($trip));

            return ApiResponse::sendResponseSuccess([], 'Trip completed successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    // Rider submits review (rating + notes + tip)
    public function submitRiderReview($id, Request $request)
    {
        // Validate: rating, notes, tip_amount
        // Process rider review
    }

    // Driver submits review (rating + notes)
    public function submitDriverReview($id, Request $request)
    {
        // Validate: rating, notes
        // Process driver review
    }

    public function cancelTripByRider(Request $request)
    {
        try {
            $trip = Trip::findOrFail($request->trip_id);
            $trip->cancelByRider();

            event(new TripCancelledByRider($trip));

            return ApiResponse::sendResponseSuccess([], 'Trip cancelled by rider successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }


    public function cancelTripByDriver(Request $request)
    {
        try {
            $trip = Trip::findOrFail($request->trip_id);
            $trip->cancelByDriver();

            event(new TripCancelledByDriver($trip));

            return ApiResponse::sendResponseSuccess([], 'Trip cancelled by driver successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function store(TripRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $rider = auth('rider-api')->user();
            $tripData = [
                'rider_id' => $rider->id,
                'trip_type_id' => $request->trip_type_id,
                'coupon_id' => $request->coupon_id,
                'requested_time' => $request->requested_time,
                'payment_method_id' => $request->payment_method_id,
                'trip_status_id' => $this->trip_status_service->search_trip_status(TripStatusEnum::Searching)->id,
            ];
            $trip = $this->trip_service->create($tripData);

            $locations = $data['locations'];

            foreach ($locations as $location) {
                TripLocation::create([
                    'location' => $location['location'],
                    'location_order' => $location['location_order'],
                    'location_type' => $location['location_type'],
                    'trip_id' => $trip->id,
                ]);
            }
            DB::commit(); // Never reached
            fireAndForgetRequest(config('functions.driver_search_url'), [
                'headers' => [
                    'X-Driver-Search-Secret' => config('functions.driver_search_secret'),
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['trip_id' => $trip->id]),
            ]);
            return ApiResponse::sendResponseSuccess(
                new TripResource($trip),
                trans_fallback('messages.trip.created', 'Trip Created successfully'),
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'trip Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $trip = $this->trip_service->findById($id);
            return ApiResponse::sendResponseSuccess(data: new TripResource($trip), message: trans_fallback('messages.trip.retrieved', 'trip Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripRequest $request, string $id)
    {
        try {
            $trip = $this->trip_service->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new TripResource($trip),
                trans_fallback('messages.trip.updated', 'trip updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'Update failed: ' . $e->getMessage())
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->trip_service->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip.deleted', 'trip updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }
}

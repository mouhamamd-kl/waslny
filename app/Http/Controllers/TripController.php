<?php

namespace App\Http\Controllers;

use App\Enums\LocationTypeEnum;
use App\Enums\TripStatusEnum;
use App\Enums\TripTypeEnum;
use App\Events\TripCancelledByDriver;
use App\Events\TripCancelledByRider;
use App\Events\TripCompleted;
use App\Events\TripCreated;
use App\Helpers\ApiResponse;
use App\Http\Requests\SubmitDriverReviewRequest;
use App\Http\Requests\SubmitRiderReviewRequest;
use App\Http\Requests\TripRequest;
use App\Http\Requests\TripSearchRequest;
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
    public function search(TripSearchRequest $request)
    {
        try {

            $filters = $request->validated();
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


    public function completeTrip($id)
    {
        try {
            $trip = $this->trip_service->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
            $trip->completeTrip();

            event(new TripCompleted($trip));

            return ApiResponse::sendResponseSuccess([], 'Trip completed successfully.', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    // Rider submits review (rating + notes + tip)
    public function submitRiderReview($id, SubmitRiderReviewRequest $request)
    {
        $trip = $this->trip_service->findById($id);
        if (!$trip) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
        }

        $data = $request->validated();

        $trip->update([
            'driver_rating' => $data['rating'],
            'driver_review_notes' => $data['notes'],
            'tip_amount' => $data['tip_amount'],
        ]);

        $trip->driver->recalculateRating();

        return ApiResponse::sendResponseSuccess([], 'Review submitted successfully.');
    }

    // Driver submits review (rating + notes)
    public function submitDriverReview($id, SubmitDriverReviewRequest $request)
    {
        $trip = $this->trip_service->findById($id);
        if (!$trip) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
        }

        $data = $request->validated();

        $trip->update([
            'rider_rating' => $data['rating'],
            'rider_review_notes' => $data['notes'],
        ]);

        $trip->rider->recalculateRating();

        return ApiResponse::sendResponseSuccess([], 'Review submitted successfully.');
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
    // trip_flow
    public function store(TripRequest $request)
    {
        try {
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = auth('rider-api')->user();

            if ($rider->trips()->whereHas('status', function ($query) {
                $query->where('name', '!=', TripStatusEnum::Completed->value);
            })->exists()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip.error.already_in_trip', 'You are already in a trip')
                );
            }

            $trip = null;
            DB::transaction(function () use ($request, &$trip, $rider) {
                $data = $request->validated();
                $tripType = TripTypeEnum::tryFrom($request->trip_type_id);

                $tripData = [
                    'rider_id' => $rider->id,
                    'trip_type_id' => $request->trip_type_id,
                    'coupon_id' => $request->coupon_id,
                    'requested_time' => $request->requested_time,
                    'payment_method_id' => $request->payment_method_id,
                    'trip_status_id' => $this->trip_status_service->search_trip_status(TripStatusEnum::Searching)->id,
                    'search_started_at' => now(),
                    'search_expires_at' => now()->addMinutes(5)
                ];
                $trip = $this->trip_service->create($tripData);

                $locations = $data['locations'];
                $pickupLocation = null;

                foreach ($locations as $key => $location) {
                    if ($tripType === TripTypeEnum::ROUND_TRIP && $location['location_type'] === LocationTypeEnum::DropOff->value) {
                        $locations[$key]['location_type'] = LocationTypeEnum::Stop->value;
                    }
                    $tripLocation = TripLocation::create([
                        'location' => $location['location'],
                        'location_order' => $location['location_order'],
                        'location_type' => $locations[$key]['location_type'],
                        'trip_id' => $trip->id,
                    ]);
                    if ($location['location_type'] === LocationTypeEnum::Pickup->value) {
                        $pickupLocation = $tripLocation;
                    }
                }

                if ($tripType === TripTypeEnum::ROUND_TRIP && $pickupLocation) {
                    TripLocation::create([
                        'location' => $pickupLocation->location,
                        'location_order' => count($locations) + 1,
                        'location_type' => LocationTypeEnum::DropOff->value,
                        'trip_id' => $trip->id,
                    ]);
                }
            });

            // Dispatch the event after the transaction has been successfully committed
            if ($trip) {
                event(new TripCreated($trip));
            }

            return ApiResponse::sendResponseSuccess(
                new TripResource($trip),
                trans_fallback('messages.trip.created', 'Trip Created successfully'),
                201
            );
        } catch (Exception $e) {
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
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
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
            $trip = $this->trip_service->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
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
            $trip = $this->trip_service->findById($id);
            if (!$trip) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip not found'), 404);
            }
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

    // public function findDriverForTrip(Request $request)
    // {
    //     // Authorization
    //     if ($request->header('X-Driver-Search-Secret') !== env('DRIVER_SEARCH_SECRET')) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $tripId = $request->input('trip_id');
    //     $trip = Trip::with('rider')->findOrFail($tripId);

    //     $result = $this->trip_service->findDriverForTrip($trip);

    //     return response()->json($result);
    // }
}

<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Rider;
use App\Models\Trip;
use App\Models\TripStatus;
use App\Enums\TripStatusEnum;
use App\Events\SearchTimeout;
use App\Events\TripAvailableForDriver;
use App\Models\Driver;
use App\Models\TripDriverNotification;
use App\Models\TripTimeType;
use App\Models\TripType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class TripService extends BaseService
{
    protected array $relations = ['driver', 'rider', 'status', 'type', 'timeType', 'locations', 'paymentMethod', 'notifications', 'notifiedDrivers', 'riderCoupon'];

    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Trip, $cache);
    }

    public function searchTrips(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                filters: $filters,
                relations: $this->relations,
                perPage: $perPage,
                columns: ['*'],
                withCount: []
            );
    }

    public function findTripById($id): ?Trip
    {
        return $this->findById(id: $id, relations: $this->relations);
    }

    public function findDriverForTrip(Trip $trip): array
    {
        // If driver already found, stop searching
        if ($trip->driver_id) {
            return [
                'status' => 'completed',
                'result' => 'driver_found',
                'driver_id' => $trip->driver_id
            ];
        }

        // Initialize search if first attempt
        if (!$trip->search_started_at) {
            $trip->update([
                'search_started_at' => now(),
                'search_expires_at' => now()->addMinutes(5)
            ]);
        }

        // Find available drivers within radius
        $drivers = $this->findNearbyDrivers($trip);

        // Send notifications to found drivers
        foreach ($drivers as $driver) {
            $this->notifyDriver($trip, $driver);
        }

        // Check for accepted drivers
        if ($acceptedDriver = $this->checkForAcceptedDriver($trip)) {
            $trip->update(['driver_id' => $acceptedDriver->id]);
            return [
                'status' => 'completed',
                'result' => 'driver_accepted',
                'driver_id' => $acceptedDriver->id
            ];
        }

        // Expand search radius if no drivers found
        if ($drivers->isEmpty()) {
            $trip->increment('driver_search_radius', 1000); // Expand by 1km
        }

        // Check if search should continue
        if ($trip->search_expires_at->isPast()) {
            $trip->update(['trip_status_id' => TripStatusEnum::SystemCancelled->value]);
            event(new SearchTimeout($trip));
            return [
                'status' => 'completed',
                'result' => 'search_expired'
            ];
        }

        // Queue next search attempt
        $this->queueNextSearch($trip);

        return [
            'status' => 'searching',
            'radius' => $trip->driver_search_radius,
            'notified_drivers' => $drivers->pluck('id')
        ];
    }

    private function findNearbyDrivers(Trip $trip): Collection
    {
        $pickupLocation = $trip->locations()->pickupPoints()->first();

        if (!$pickupLocation) {
            return collect();
        }

        $rider = $trip->rider;

        return Driver::where('driver_status_id', 'available') // Available status
            ->when($rider && $rider->rating !== null, function ($query) use ($rider) {
                $minRating = max(0, $rider->rating - 1);
                $maxRating = min(5, $rider->rating + 1);
                return $query->whereBetween('rating', [$minRating, $maxRating]);
            })
            ->notNotifiedForTrip($trip)
            ->whereRaw(
                "ST_DWithin(location, ?, ?)",
                [
                    $pickupLocation->location->toWkt(),
                    $trip->driver_search_radius
                ]
            )
            ->orderByRaw("rating DESC, ST_Distance(location, ?)", [$pickupLocation->location->toWkt()])
            ->limit(5)
            ->get();
    }

    private function notifyDriver(Trip $trip, Driver $driver): void
    {
        // Send push notification to driver
        $driver->notify(new TripAvailableForDriver($trip, $driver->id));

        // Record notification
        TripDriverNotification::create([
            'trip_id' => $trip->id,
            'driver_id' => $driver->id,
            'sent_at' => now(),
        ]);
    }

    private function checkForAcceptedDriver(Trip $trip): ?Driver
    {
        return TripDriverNotification::where('trip_id', $trip->id)
            ->where('status', 'accepted')
            ->first()
            ?->driver;
    }

    private function queueNextSearch(Trip $trip): void
    {
        // Queue next search attempt in 15 seconds
        dispatch(function () use ($trip) {
            Http::withHeaders([
                'X-Driver-Search-Secret' => env('DRIVER_SEARCH_SECRET')
            ])->post(config('app.url') . '/trip/find-driver', [
                'trip_id' => $trip->id
            ]);
        })->delay(now()->addSeconds(15));
    }
}

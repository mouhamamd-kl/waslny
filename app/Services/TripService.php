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
use App\Models\TripLocation;
use App\Models\TripTimeType;
use App\Models\TripType;
use App\Enums\LocationTypeEnum;
use App\Enums\TripTypeEnum;
use Clickbar\Magellan\Data\Geometries\Point;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class TripService extends BaseService
{
    protected array $relations = ['driver', 'rider', 'status', 'type', 'timeType', 'locations', 'paymentMethod', 'notifications', 'notifiedDrivers', 'riderCoupon', 'routeLocations'];
    protected DriverSearchService $driverSearchService;

    public function __construct(CacheHelper $cache, DriverSearchService $driverSearchService)
    {
        parent::__construct(new Trip, $cache);
        $this->driverSearchService = $driverSearchService;
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

    // trip_flow
    public function findAndAssignDriver(Trip $trip): ?Driver
    {
        // Find available drivers within radius
        $drivers = $this->driverSearchService->findNearbyDrivers($trip);

        // Send notifications to found drivers
        foreach ($drivers as $driver) {
            $this->notifyDriver($trip, $driver);
        }

        // Check for accepted drivers
        if ($acceptedDriver = $this->checkForAcceptedDriver($trip)) {
            // $trip->update(['driver_id' => $acceptedDriver->id]);
            return $acceptedDriver;
        }

        // Expand search radius if no drivers found
        if ($drivers->isEmpty()) {
            $trip->increment('driver_search_radius', 1000); // Expand by 1km
        }

        return null;
    }

    // public function findDriverForTrip(Trip $trip): array
    // {
    //     // If driver already found, stop searching
    //     if ($trip->driver_id) {
    //         return [
    //             'status' => 'completed',
    //             'result' => 'driver_found',
    //             'driver_id' => $trip->driver_id
    //         ];
    //     }

    //     // Initialize search if first attempt
    //     if (!$trip->search_started_at) {
    //         $trip->update([
    //             'search_started_at' => now(),
    //             'search_expires_at' => now()->addMinutes(5)
    //         ]);
    //     }

    //     // Find available drivers within radius
    //     $drivers = $this->findNearbyDrivers($trip);

    //     // Send notifications to found drivers
    //     foreach ($drivers as $driver) {
    //         $this->notifyDriver($trip, $driver);
    //     }

    //     // Check for accepted drivers
    //     if ($acceptedDriver = $this->checkForAcceptedDriver($trip)) {
    //         $trip->update(['driver_id' => $acceptedDriver->id]);
    //         return [
    //             'status' => 'completed',
    //             'result' => 'driver_accepted',
    //             'driver_id' => $acceptedDriver->id
    //         ];
    //     }

    //     // Expand search radius if no drivers found
    //     if ($drivers->isEmpty()) {
    //         $trip->increment('driver_search_radius', 1000); // Expand by 1km
    //     }

    //     // Check if search should continue
    //     if ($trip->search_expires_at->isPast()) {
    //         $trip->update(['trip_status_id' => TripStatusEnum::SystemCancelled->value]);
    //         event(new SearchTimeout($trip));
    //         return [
    //             'status' => 'completed',
    //             'result' => 'search_expired'
    //         ];
    //     }

    //     // Queue next search attempt
    //     $this->queueNextSearch($trip);

    //     return [
    //         'status' => 'searching',
    //         'radius' => $trip->driver_search_radius,
    //         'notified_drivers' => $drivers->pluck('id')
    //     ];
    // }

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
        return $trip->fresh()->driver;
    }

    public function getDriversNotAccepting(Trip $trip, Driver $driver): Collection
    {
        return Driver::whereIn('id', function ($query) use ($trip, $driver) {
            $query->select('driver_id')
                ->from('trip_driver_notifications')
                ->where('trip_id', $trip->id)
                ->where('driver_id', '!=', $driver->id);
        })->get();
    }

    public function createTripLocations(Trip $trip, array $locationsData): void
    {
        $tripType = TripTypeEnum::tryFrom($trip->trip_type_id);
        $tripLocations = [];
        $pickupLocation = null;

        foreach ($locationsData as $locationData) {
            $locationType = $locationData['location_type'];
            if ($tripType === TripTypeEnum::ROUND_TRIP && $locationType === LocationTypeEnum::DropOff->value) {
                $locationType = LocationTypeEnum::Stop->value;
            }

            $tripLocations[] = [
                'location' => Point::makeGeodetic($locationData['location']['coordinates'][0], $locationData['location']['coordinates'][1]),
                'location_order' => $locationData['location_order'],
                'location_type' => $locationType,
                'trip_id' => $trip->id,
            ];

            if ($locationData['location_type'] === LocationTypeEnum::Pickup->value) {
                $pickupLocation = $tripLocations[count($tripLocations) - 1];
            }
        }

        if ($tripType === TripTypeEnum::ROUND_TRIP && $pickupLocation) {
            $tripLocations[] = [
                'location' => $pickupLocation['location'],
                'location_order' => count($locationsData) + 1,
                'location_type' => LocationTypeEnum::DropOff->value,
                'trip_id' => $trip->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        TripLocation::insert($tripLocations);
    }


    public function createTrip($data)
    {
        return Trip::create(
            [
                'rider_id' => $data->rider_id,
                'driver_id' => $data->driver_id,

            ]
        );
    }

    // private function queueNextSearch(Trip $trip): void
    // {
    //     // Queue next search attempt in 15 seconds
    //     dispatch(function () use ($trip) {
    //         Http::withHeaders([
    //             'X-Driver-Search-Secret' => env('DRIVER_SEARCH_SECRET')
    //         ])->post(config('app.url') . '/trip/find-driver', [
    //             'trip_id' => $trip->id
    //         ]);
    //     })->delay(now()->addSeconds(15));
    // }

    public function calculateTripFine($data)
    {
        $carServiceLevel = app(CarServiceLevelService::class)->findById($data['car_service_level_id']);
        $pricing = $carServiceLevel->getCurrentPricing();

        $locations = $data['locations'];
        $totalDistance = 0;

        for ($i = 0; $i < count($locations) - 1; $i++) {
            $point1 = $locations[$i]['location'];
            $point2 = $locations[$i + 1]['location'];
            $totalDistance += $point1->distance($point2);
        }

        return $pricing->calculateFare($totalDistance / 1000);
    }
}

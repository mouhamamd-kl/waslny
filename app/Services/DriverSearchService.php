<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

use Clickbar\Magellan\Database\PostgisFunctions\ST;

class DriverSearchService
{
    public function findNearbyDrivers(Trip $trip): Collection
    {
        Log::info("DriverSearchService: Starting findNearbyDrivers for trip ID: {$trip->id}");

        $pickupLocation = $trip->pickup_location;

        if (!$pickupLocation) {
            Log::warning("DriverSearchService: No pickup location found for trip ID: {$trip->id}");
            return collect();
        }

        Log::info("DriverSearchService: Pickup location for trip ID: {$trip->id} is: Lat: " . $pickupLocation->location->getLatitude() . ", Lng: " . $pickupLocation->location->getLongitude());
        Log::info("DriverSearchService: Search radius for trip ID: {$trip->id} is: {$trip->driver_search_radius}");

        $query = Driver::query()
            ->availableForTrip($trip)
            ->where(ST::distanceSphere($trip->pickup_location->location, 'location'), '<=', $trip->driver_search_radius)
            ->orderBy('rating', 'desc')
            ->limit(100);

        Log::info("DriverSearchService: SQL query for trip ID: {$trip->id} is: " . $query->toSql());

        $drivers = $query->get();

        Log::info("DriverSearchService: Found " . count($drivers) . " drivers for trip ID: {$trip->id}");

        return $drivers;
    }
}

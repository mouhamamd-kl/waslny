<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;

use Clickbar\Magellan\Database\PostgisFunctions\ST;

class DriverSearchService
{
    public function findNearbyDrivers(Trip $trip): Collection
    {
        $pickupLocation = $trip->locations()->pickupPoints()->first();

        if (!$pickupLocation) {
            return collect();
        }

        return Driver::query()
            ->availableForTrip($trip)
            ->stWhere(ST::distanceSphere($pickupLocation->location, 'location'), '<=', $trip->driver_search_radius)
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();
    }
}

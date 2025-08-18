<?php

namespace App\Services;

use App\Models\Trip;
use App\Helpers\Haversine;

class FareCalculationService
{
    public function calculateFare(Trip $trip)
    {
        $totalDistance = $this->calculateTotalDistance($trip);
        $pricing = $trip->carServiceLevel->pricing;

        $baseFare = $pricing->base_fare;
        $perKilometerRate = $pricing->per_kilometer_rate;
        $perMinuteRate = $pricing->per_minute_rate;

        $distanceCost = $totalDistance * $perKilometerRate;
        $durationCost = $this->calculateDurationCost($trip, $perMinuteRate);

        $fare = $baseFare + $distanceCost + $durationCost;

        if ($trip->coupon) {
            $fare = $this->applyCoupon($fare, $trip->coupon);
        }

        return $fare;
    }

    private function calculateTotalDistance(Trip $trip)
    {
        $totalDistance = 0;
        $locations = $trip->routeLocations;

        for ($i = 0; $i < count($locations) - 1; $i++) {
            $totalDistance += Haversine::distance(
                $locations[$i]->latitude,
                $locations[$i]->longitude,
                $locations[$i + 1]->latitude,
                $locations[$i + 1]->longitude
            );
        }

        return $totalDistance;
    }

    private function calculateDurationCost(Trip $trip, $perMinuteRate)
    {
        $startTime = $trip->created_at;
        $endTime = now();
        $durationInMinutes = $endTime->diffInMinutes($startTime);

        return $durationInMinutes * $perMinuteRate;
    }

    private function applyCoupon($fare, $coupon)
    {
        if ($coupon->isActive()) {
            $coupon->recordUsage();
            return $coupon->applyDiscount($fare);
        }

        return $fare;
    }
}

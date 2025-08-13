<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Driver;
use App\Models\Rider;
use App\Models\Trip;
use Carbon\Carbon;

class DashboardService
{
    public function getHeaderStats()
    {
        return [
            'drivers_count' => Driver::count(),
            'riders_count' => Rider::count(),
            'trips_count' => Trip::count(),
            'coupons_count' => Coupon::count(),
        ];
    }

    public function getChartStats(string $period)
    {
        $endDate = Carbon::now();
        $startDate = $this->calculateStartDate($period, $endDate);

        return [
            'period' => $period,
            'trips_requests' => $this->getTripStatsForPeriod($startDate, $endDate),
            'active_riders' => $this->getActiveRiderStatsForPeriod($startDate, $endDate),
            'active_drivers' => $this->getActiveDriverStatsForPeriod($startDate, $endDate),
        ];
    }

    private function calculateStartDate(string $period, Carbon $endDate): Carbon
    {
        // For now, only last_60_minutes is supported as per the design
        return $endDate->copy()->subMinutes(60);
    }

    private function getTripStatsForPeriod(Carbon $startDate, Carbon $endDate)
    {
        $trips = Trip::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(created_at, 'HH24:MI') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get();

        return [
            'total' => $trips->sum('value'),
            'series' => $trips,
        ];
    }

    private function getActiveDriverStatsForPeriod(Carbon $startDate, Carbon $endDate)
    {
        // Assuming 'active' means their status was updated recently.
        // This might need adjustment based on the actual logic for "active".
        $drivers = Driver::where('last_active_at', '>=', $startDate)
            ->selectRaw("TO_CHAR(last_active_at, 'HH24:MI') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get();

        return [
            'total' => $drivers->sum('value'),
            'series' => $drivers,
        ];
    }

    private function getActiveRiderStatsForPeriod(Carbon $startDate, Carbon $endDate)
    {
        // Assuming 'active' means they created a trip recently.
        // This is a proxy for activity.
        $riders = Trip::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('rider_id')
            ->count('rider_id');

        // Chart data for active riders is more complex and might require a different approach.
        // For now, returning a simplified version.
        return [
            'total' => $riders,
            'series' => [], // Placeholder for series data
        ];
    }
}

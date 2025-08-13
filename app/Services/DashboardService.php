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
            'trips_requests' => $this->getTripStatsForPeriod($startDate, $endDate, $period),
            'active_riders' => $this->getActiveRiderStatsForPeriod($startDate, $endDate, $period),
            'active_drivers' => $this->getActiveDriverStatsForPeriod($startDate, $endDate, $period),
        ];
    }

    private function calculateStartDate(string $period, Carbon $endDate): Carbon
    {
        return match ($period) {
            'last_60_minutes' => $endDate->copy()->subMinutes(60),
            'last_12_hours' => $endDate->copy()->subHours(12),
            'last_24_hours' => $endDate->copy()->subHours(24),
            'last_7_days' => $endDate->copy()->subDays(7),
            default => $endDate->copy()->subMinutes(60),
        };
    }

    private function getTripStatsForPeriod(Carbon $startDate, Carbon $endDate, string $period)
    {
        $format = $period === 'last_7_days' ? 'YYYY-MM-DD' : 'HH24:MI';

        $trips = Trip::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(created_at, '$format') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get();

        return [
            'total' => $trips->sum('value'),
            'series' => $trips,
        ];
    }

    private function getActiveDriverStatsForPeriod(Carbon $startDate, Carbon $endDate, string $period)
    {
        $format = $period === 'last_7_days' ? 'YYYY-MM-DD' : 'HH24:MI';

        // Assuming 'active' means their status was updated recently or is null (currently online).
        $drivers = Driver::where(function ($query) use ($startDate) {
            $query->where('last_seen_at', '>=', $startDate)
                ->orWhereNull('last_seen_at');
        })
            ->selectRaw("TO_CHAR(COALESCE(last_seen_at, NOW()), '$format') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get();

        return [
            'total' => $drivers->sum('value'),
            'series' => $drivers,
        ];
    }

    private function getActiveRiderStatsForPeriod(Carbon $startDate, Carbon $endDate, string $period)
    {
        $format = $period === 'last_7_days' ? 'YYYY-MM-DD' : 'HH24:MI';

        // Assuming 'active' means their status was updated recently or is null (currently online).
        $riders = Rider::where(function ($query) use ($startDate) {
            $query->where('last_seen_at', '>=', $startDate)
                ->orWhereNull('last_seen_at');
        })
            ->selectRaw("TO_CHAR(COALESCE(last_seen_at, NOW()), '$format') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get();

        return [
            'total' => $riders->sum('value'),
            'series' => $riders,
        ];
    }
}

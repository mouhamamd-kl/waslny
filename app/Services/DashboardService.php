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
        $dateRange = $this->generateDateRange($startDate, $endDate, $period);

        $format = $this->getSqlFormat($period);

        $trips = Trip::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(created_at, '$format') as time, COUNT(*) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get()
            ->keyBy('time');

        $series = $dateRange->map(function ($value, $time) use ($trips) {
            return [
                'time' => $time,
                'value' => $trips->has($time) ? $trips->get($time)->value : 0,
            ];
        })->values();

        return [
            'total' => $trips->sum('value'),
            'series' => $series,
        ];
    }

    private function getActiveDriverStatsForPeriod(Carbon $startDate, Carbon $endDate, string $period)
    {
        $dateRange = $this->generateDateRange($startDate, $endDate, $period);
        $format = $this->getSqlFormat($period);

        // Assuming 'active' means their status was updated recently or is null (currently online).
        $drivers = Driver::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('last_seen_at', [$startDate, $endDate]);
        })
            ->selectRaw("TO_CHAR(last_seen_at, '$format') as time, COUNT(DISTINCT id) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get()
            ->keyBy('time');

        $series = $dateRange->map(function ($value, $time) use ($drivers) {
            return [
                'time' => $time,
                'value' => $drivers->has($time) ? $drivers->get($time)->value : 0,
            ];
        })->values();


        return [
            'total' => $drivers->sum('value'),
            'series' => $series,
        ];
    }

    private function getActiveRiderStatsForPeriod(Carbon $startDate, Carbon $endDate, string $period)
    {
        $dateRange = $this->generateDateRange($startDate, $endDate, $period);
        $format = $this->getSqlFormat($period);

        // Assuming 'active' means their status was updated recently or is null (currently online).
        $riders = Rider::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('last_seen_at', [$startDate, $endDate]);
        })
            ->selectRaw("TO_CHAR(last_seen_at, '$format') as time, COUNT(DISTINCT id) as value")
            ->groupBy('time')
            ->orderBy('time', 'asc')
            ->get()
            ->keyBy('time');

        $series = $dateRange->map(function ($value, $time) use ($riders) {
            return [
                'time' => $time,
                'value' => $riders->has($time) ? $riders->get($time)->value : 0,
            ];
        })->values();

        return [
            'total' => $riders->sum('value'),
            'series' => $series,
        ];
    }

    private function generateDateRange(Carbon $startDate, Carbon $endDate, string $period)
    {
        $range = collect();
        $current = $startDate->copy();

        [$interval, $format] = match ($period) {
            'last_60_minutes' => ['addMinute', 'H:i'],
            'last_12_hours', 'last_24_hours' => ['addHour', 'H:00'],
            'last_7_days' => ['addDay', 'Y-m-d'],
            default => ['addMinute', 'H:i'],
        };

        while ($current <= $endDate) {
            $range->put($current->format($format), 0);
            $current->$interval();
        }

        return $range;
    }

    private function getSqlFormat(string $period): string
    {
        return match ($period) {
            'last_60_minutes' => 'HH24:MI',
            'last_12_hours', 'last_24_hours' => 'HH24:00',
            'last_7_days' => 'YYYY-MM-DD',
            default => 'HH24:MI',
        };
    }
}

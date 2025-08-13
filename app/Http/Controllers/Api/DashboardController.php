<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Http\Requests\Dashboard\GetChartStatsRequest;
use App\Helpers\ApiResponse;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getHeaderStats()
    {
        $stats = $this->dashboardService->getHeaderStats();
        return ApiResponse::sendResponseSuccess($stats);
    }

    public function getChartStats(GetChartStatsRequest $request)
    {
        $period = $request->validated('period', 'last_60_minutes');
        $stats = $this->dashboardService->getChartStats($period);
        return ApiResponse::sendResponseSuccess($stats);
    }
}

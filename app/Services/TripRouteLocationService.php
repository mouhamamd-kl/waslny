<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\domains\trips\trip_route_locations\TripRouteLocation;
use App\Models\Rider;
use App\Models\RiderFolder;
use App\Models\RiderSavedLocation;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class TripRouteLocationService extends BaseService
{
    protected array $relations = ['trip'];
    protected $tripService;
    public function __construct(CacheHelper $cache, TripService $tripService)
    {
        parent::__construct(new TripRouteLocation, $cache);
        $this->tripService = $tripService;
    }

    public function searchTripRouteLocations(
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
}

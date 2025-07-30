<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Rider;
use App\Models\RiderFolder;
use App\Models\RiderSavedLocation;
use App\Models\TripLocation;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class TripLocationService extends BaseService
{
    protected array $relations = ['trip'];
    protected $tripService;
    public function __construct(CacheHelper $cache, TripService $tripService)
    {
        parent::__construct(new TripLocation, $cache);
        $this->tripService = $tripService;
    }

    public function searchTripLocations(
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
    public function getNextPendingTripLocation(int $tripId): ?Model
    {
        return $this->getNextPendingTripLocations($tripId)->first();
    }
    public function getNextPendingTripLocations(int $tripId): ?Collection
    {
        $trip = $this->tripService->findById($tripId);
        if (!$trip) {
            throw new ModelNotFoundException("Trip {$tripId} not found");
        }
        return $trip->locations()
            ->pending()
            ->orderBy('location_order')
            ->get();
    }
}

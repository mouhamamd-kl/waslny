<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Rider;
use App\Models\Trip;
use App\Models\TripStatus;
use App\Models\TripTimeType;
use App\Models\TripType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TripService extends BaseService
{
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Trip, $cache);
    }

    public function searchTrips(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                $filters,
                ['driver', 'rider', 'status', 'type', 'timeType', 'locations', 'paymentMethod'], // relations if any
                $perPage,
                ['*'],
                [] // <-- Here is your withCount
            );
    }

    public function findTripById($id): ?Trip
    {
        $relations = ['driver', 'rider', 'status', 'type', 'timeType', 'locations', 'paymentMethod'];
        return $this->findById(id: $id, relations: $relations);
    }
}

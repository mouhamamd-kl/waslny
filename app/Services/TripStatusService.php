<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Rider;
use App\Models\TripStatus;
use App\Models\TripTimeType;
use App\Models\TripType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TripStatusService extends BaseService
{
    protected array $relations = ['trips'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new TripStatus, $cache);
    }

    public function searchTripStatus(
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
    public function deActivate($tripTypeId)
    {
        try {
            /** @var TripType $tripType */ // Add PHPDoc type hint
            $tripType = $this->findById($tripTypeId);
            $tripType->deActivate();
        } catch (Exception $e) {
            throw new Exception('error deActivating TripType' . $e);
        }
    }

    public function activate($driverId)
    {
        try {
            /** @var TripType $tripType */ // Add PHPDoc type hint
            $tripType = $this->findById($driverId);
            $tripType->deActivate();
        } catch (Exception $e) {
            throw new Exception('error activating TripType' . $e);
        }
    }

    public function search_trip_status($name)
    {
        try {
            /** @var TripType $tripType */ // Add PHPDoc type hint
            return  $tripType = $this->search_first(['system_value' => $name]);
        } catch (Exception $e) {
            throw new Exception('error Searching Trip Status' . $e);
        }
    }

    public function searchBySystemValue(String $system_value): ?TripStatus
    {
        return $this->search_first(filters: ['system_value' => $system_value]);
    }
}

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
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new TripStatus, $cache);
    }

    public function searchTripTimeType(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                $filters,
                [], // relations if any
                $perPage,
                ['*'],
                [] // <-- Here is your withCount
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
}

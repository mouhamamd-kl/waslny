<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Rider;
use App\Models\TripType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TripTypeService extends BaseService
{
    protected array $relations = ['trips'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new TripType, $cache);
    }

    public function searchTripType(
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

    public function searchBySystemValue(String $system_value): ?TripType
    {
        return $this->search_first(filters: ['system_value' => $system_value]);
    }
}

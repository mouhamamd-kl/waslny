<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\CarManufacturer;
use App\Models\CarModel;
use Illuminate\Pagination\LengthAwarePaginator;

class CarModelService extends BaseService
{
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new CarModel, $cache);
    }

    public function searchCarModel(
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
}

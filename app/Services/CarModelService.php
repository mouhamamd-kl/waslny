<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\CarManufacturer;
use App\Models\CarModel;
use Illuminate\Pagination\LengthAwarePaginator;

class CarModelService extends BaseService
{
    protected array $relations = ['manufacturer', 'serviceLevel', 'driversCars'];
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
                filters: $filters,
                relations: $this->relations,
                perPage: $perPage,
                columns: ['*'],
                withCount: []
            );
    }
}

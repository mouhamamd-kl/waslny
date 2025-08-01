<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\CarManufacturer;
use Illuminate\Pagination\LengthAwarePaginator;

class CarManufactureService extends BaseService
{
    protected array $relations = ['country', 'models'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new CarManufacturer, $cache);
    }

    public function searchCarManufacture(
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

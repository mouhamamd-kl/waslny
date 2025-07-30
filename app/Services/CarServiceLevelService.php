<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\CarServiceLevel;
use Illuminate\Pagination\LengthAwarePaginator;

class CarServiceLevelService extends BaseService
{
    protected array $relations = ['carModels', 'pricings'];

    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new CarServiceLevel, $cache);
    }

    public function searchCarServiceLevel(
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

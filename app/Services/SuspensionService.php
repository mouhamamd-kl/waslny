<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Suspension;
use Illuminate\Pagination\LengthAwarePaginator;

class SuspensionService extends BaseService
{
    protected array $relations=[];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Suspension, $cache);
    }

    public function searchSuspension(
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

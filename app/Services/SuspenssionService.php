<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Suspension;
use Illuminate\Pagination\LengthAwarePaginator;

class SuspenssionService extends BaseService
{
    protected array $relations = [];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Suspension, $cache);
    }

    public function searchSuspenssions(
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

    public function searchByReason(String $reason): ?Suspension
    {
        return $this->search_first(filters: ['reason' => $reason]);
    }

    public function searchBySystemValue(String $system_value): ?Suspension
    {
        return $this->search_first(filters: ['system_value' => $system_value]);
    }
}

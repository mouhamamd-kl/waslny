<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Suspension;
use Illuminate\Pagination\LengthAwarePaginator;

class SuspenssionService extends BaseService
{
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
                $filters,
                [], // relations if any
                $perPage,
                ['*'],
                [] // <-- Here is your withCount
            );
    }

    public function searchByReason(String $reason): ?Suspension
    {
        return $this->search_first(filters: ['reason' => $reason]);
    }
}

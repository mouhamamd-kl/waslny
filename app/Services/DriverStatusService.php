<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\DriverStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverStatusService extends BaseService
{
    protected array $relations = ['drivers'];

    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new DriverStatus, $cache);
    }

    public function searchDriverStatus(
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

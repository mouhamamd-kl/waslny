<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminService extends BaseService
{
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Admin, $cache);
    }

    public function searchAdmins(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                filters: $filters,
                relations: [],
                perPage: $perPage,
                columns: ['*'],
                withCount: [] // <-- Here is your withCount
            );
    }
}

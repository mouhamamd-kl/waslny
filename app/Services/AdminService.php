<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;

class CarManufactureService extends BaseService
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
                $filters,
                [], // relations if any
                $perPage,
                ['*'],
                [] // <-- Here is your withCount
            );
    }
}

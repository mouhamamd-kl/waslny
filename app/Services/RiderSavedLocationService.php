<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Rider;
use App\Models\RiderFolder;
use App\Models\RiderSavedLocation;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RiderSavedLocationService extends BaseService
{
    protected array $relations = ['rider', 'folder'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new RiderSavedLocation, $cache);
    }
    public function searchRiderSavedLocations(
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

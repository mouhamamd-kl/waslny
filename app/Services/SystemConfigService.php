<?php

namespace App\Services;

use App\Models\SystemConfig;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class SystemConfigService extends BaseService
{
    public function __construct(SystemConfig $model)
    {
        parent::__construct($model);
    }

    public function searchSystemConfigs(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->paginatedList(
            filters: $filters,
            perPage: $perPage
        );
    }
}

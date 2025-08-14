<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\Rider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentMethodService extends BaseService
{
    protected array $relations = ['riders', 'trips'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new PaymentMethod, $cache);
    }

    public function searchPaymentMethods(
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

    public function searchByCode(String $code): ?PaymentMethod
    {
        return $this->search_first(filters: ['code' => $code]);
    }
}

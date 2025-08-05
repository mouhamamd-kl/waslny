<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Rider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponService extends BaseService
{
    protected array $relations = [];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Coupon, $cache);
    }

    public function searchCoupons(
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
    public static function findByCode(string $code): Coupon
    {
        return Coupon::where('code', $code)->first();
    }
}

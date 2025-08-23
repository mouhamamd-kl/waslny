<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Rider;
use App\Models\RiderCoupon;
use App\Models\RiderFolder;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiderCouponService extends BaseService
{
    protected array $relations = ['rider', 'coupon'];

    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new RiderCoupon, $cache);
    }
    public function searchRiderCoupons(
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

    public function searchRiderActiveCoupons(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        $filters['coupon.active'] = [true];
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                filters: $filters,
                relations: ['rider', 'coupon'],
                perPage: $perPage,
                columns: ['*'],
                withCount: []
            );
    }
    public function deleteRiderCoupon($rider, $riderCouponId)
    {
        try {
            // return $model->notifications()->detach($modelNotificationId);
            return  $rider->coupons()->detach($riderCouponId); // Detach one listing
            // $deleted =  $this->notifiableModel::findOrFail($modelId)
            //     ->notifications()
            //     ->detach();
            // return $deleted;
        } catch (\Exception $e) {
            Log::error(Rider::class  . " notification deletion failed: " . $e->getMessage());
            throw $e;
        }
    }
}

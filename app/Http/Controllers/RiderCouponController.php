<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\RiderCouponResource;
use App\Models\Coupon;
use App\Models\RiderCoupon;
use App\Services\CouponService;
use App\Services\RiderCouponService;
use Exception;
use Illuminate\Http\Request;

class RiderCouponController extends Controller
{
    protected $RiderCouponService;
    protected $couponService;
    public function __construct(RiderCouponService $RiderCouponService, CouponService $couponService)
    {
        $this->couponService = $couponService;
        $this->RiderCouponService = $RiderCouponService;
    }

    public function index(Request $request)
    {
        try {
            $favouriteProperties = $this->RiderCouponService->searchRiderCoupons(
                $request->input('filters', ['rider_id' => auth('sanctum')->user()->id]),
                $request->input('per_page', 10)
            );

            return ApiResponse::sendResponsePaginated($favouriteProperties, RiderCouponResource::class, 'Favourites retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function store($code)
    {
        /** @var Coupon $coupon */
        $coupon = $this->couponService->findByCode($code);
        if (! $coupon || $coupon->isActive() === false) {
            return ApiResponse::sendResponseSuccess(
                null,
                trans_fallback('messages.coupon.invalid', 'Coupon Code Invalid')
            );
        }

        try {
            /** @var Rider $rider */
            $rider = auth('rider-api')->user();

            if ($rider->coupons()->syncWithoutDetaching($coupon->id)) {
                return ApiResponse::sendResponseSuccess(
                    null,
                    trans_fallback('messages.rider_coupon.created', 'Rider Coupon Code Created'),
                    201
                );
            }

            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Coupon Adding Failed'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Coupon Adding Failed') . $e->getMessage());
        }
    }

    public function destroy($id): bool
    {
        try {
            /** @var Rider $rider */
            $rider = auth('sanctum')->user();

            // First check if the listing exists
            $rider_coupon = RiderCoupon::find($id);
            if (! $rider_coupon) {
                return true;
            }

            // Check if the listing is in user's favorites
            if (! $rider->coupons()->where('listing_id', $id)->exists()) {
                return true;
            }

            // Remove from favorites
            $rider->coupons()->detach($id);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

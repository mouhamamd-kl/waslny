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



    public function activeIndex(Request $request)
    {
        try {
            $favouriteProperties = $this->RiderCouponService->searchRiderActiveCoupons(
                $request->input('filters', ['rider_id' => auth('sanctum')->user()->id]),
                $request->input('per_page', 10)
            );

            return ApiResponse::sendResponsePaginated($favouriteProperties, RiderCouponResource::class, 'Favourites retrieved successfully');
        } catch (Exception $e) {
            throw $e;
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

    public function destroy($id)
    {
        try {
            /** @var Rider $rider */
            $rider = auth('rider-api')->user();

            $riderCoupon = RiderCoupon::find($id);

            if (! $riderCoupon) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.rider_coupon.error.not_found', 'Rider coupon not found.'),
                    404
                );
            }

            if ($riderCoupon->rider_id !== $rider->id) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.forbidden', 'You are not authorized to delete this coupon.'),
                    403
                );
            }

            $riderCoupon->delete();

            return ApiResponse::sendResponseSuccess(
                null,
                trans_fallback('messages.rider_coupon.deleted', 'Rider coupon deleted successfully.'),
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider_coupon.error.delete_failed', 'Failed to delete rider coupon.'),
                500
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Coupon\CouponSearchRequest;
use App\Http\Requests\Coupon\CouponStoreRequest;
use App\Http\Requests\Coupon\CouponUpdateRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Services\CouponService;
use Exception;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $coupons = $this->couponService->searchCoupons(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $coupons,
                CouponResource::class,
                trans_fallback('messages.coupon.list', 'Coupons retrieved successfully')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function search(CouponSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn ($value) => !is_null($value));
            $coupons = $this->couponService->searchCoupons(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $coupons,
                CouponResource::class, // Add your resource class
                trans_fallback('messages.coupon.list', 'Coupons retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(CouponStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $coupon = $this->couponService->create($data);
            $coupon->refresh(); // Refresh the model to get DB defaults
            return ApiResponse::sendResponseSuccess(
                new CouponResource($coupon),
                trans_fallback('messages.coupon.created', 'Coupons Created successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Coupon Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $coupon = $this->couponService->findById($id);
            if (!$coupon) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new CouponResource($coupon), message: trans_fallback('messages.coupon.retrieved', 'Coupon Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponUpdateRequest $request, string $id)
    {
        try {
            $coupon = $this->couponService->findById($id);
            if (!$coupon) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $coupon = $this->couponService->update((int) $id, $data);
            return ApiResponse::sendResponseSuccess(
                new CouponResource($coupon),
                trans_fallback('messages.coupon.updated', 'Coupon updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'Update failed: ' . $e->getMessage())
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $coupon = $this->couponService->findById($id);
            if (!$coupon) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
            }
            $this->couponService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.coupon.deleted', 'Coupon deleted successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }

    public function deActivate($id)
    {
        try {
            /** @var Coupon $coupon */ // Add PHPDoc type hint
            $coupon = $this->couponService->findById($id);
            if (!$coupon) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
            }
            $coupon->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.coupon.deactivated', 'Coupon DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.coupon.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var Coupon $coupon */ // Add PHPDoc type hint
            $coupon = $this->couponService->findById($id);
            if (!$coupon) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Coupon not found'), 404);
            }
            $coupon->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.coupon.activated', 'Coupon Activated successfully')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.coupon.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}

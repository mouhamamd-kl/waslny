<?php

namespace App\Http\Controllers;

use App\Enums\SuspensionReason;
use App\Helpers\ApiResponse;
use App\Http\Requests\Rider\RiderSearchRequest;
use App\Http\Requests\Suspension\SuspendAccountForeverRequest;
use App\Http\Requests\Suspension\SuspendAccountTemporarilyRequest;
use App\Http\Resources\RiderResource;
use App\Models\Rider;
use App\Services\RiderService;
use Exception;
use Illuminate\Http\Request;
class RiderController extends Controller
{
    protected $riderService;

    public function __construct(RiderService $riderService)
    {
        $this->riderService = $riderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $riders = $this->riderService->searchRiders(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $riders,
                RiderResource::class,
                trans_fallback('messages.rider.list', 'Rider  retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function search(RiderSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn ($value) => !is_null($value));
            $riders = $this->riderService->searchRiders(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $riders,
                RiderResource::class, // Add your resource class
                trans_fallback('messages.rider.list', 'Rider  retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */

    public function showAdmin($id)
    {
        try {
            $rider = $this->riderService->findById($id);
            if (!$rider) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new RiderResource($rider), message: trans_fallback('messages.rider.retrieved', 'Rider Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider  not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */

    public function suspendForever($id, SuspendAccountForeverRequest $request)
    {
        try {
            $rider = $this->riderService->findById($id);
            if (!$rider) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider not found'), 404);
            }
            if ($rider->isAccountSuspended()) {
                return ApiResponse::sendResponseError(trans_fallback('messages.rider.error.already_suspended', 'Rider is already suspended.'));
            }
            $validatedData = $request->validated();
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->riderService->suspendForever(riderId: $id, suspension_id: $validatedData['suspension_id']);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.rider.suspended', 'Rider Suspended successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.error.suspension_failed', 'Suspension failed: ' . $e->getMessage())
            );
        }
    }
    public function suspendTemporarily($id, SuspendAccountTemporarilyRequest $request)
    {
        try {
            $rider = $this->riderService->findById($id);
            if (!$rider) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider not found'), 404);
            }

            if ($rider->isAccountSuspended()) {
                return ApiResponse::sendResponseError(trans_fallback('messages.rider.error.already_suspended', 'Rider is already suspended.'));
            }
            $validatedData = $request->validated();
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->riderService->suspendTemporarily(riderId: $id, suspension_id: $validatedData['suspension_id'], suspended_until: $validatedData['suspended_until']);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.rider.suspended', 'Rider Suspended successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.error.suspension_failed', 'Suspension failed: ' . $e->getMessage())
            );
        }
    }


    public function reinstate($id)
    {
        try {
            $rider = $this->riderService->findById($id);
            if (!$rider) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider not found'), 404);
            }
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->riderService->activate($id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.rider.reinstate', 'Rider Reinstate successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.error.reinstate_failed', 'Re Instate failed: ' . $e->getMessage())
            );
        }
    }
}

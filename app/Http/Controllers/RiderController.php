<?php

namespace App\Http\Controllers;

use App\Enums\SuspensionReason;
use App\Helpers\ApiResponse;
use App\Http\Requests\RiderUpdateRequest;
use App\Http\Requests\SuspendAccountForeverRequest;
use App\Http\Requests\SuspendAccountRequest;
use App\Http\Requests\SuspendAccountTemporarilyRequest;
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
                $request->input('perPage', 5)
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
    public function search(Request $request)
    {
        try {
            $filters = $request->only([
                'name',
            ]);
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
            $validatedData = $request->validate();
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->riderService->suspendForever(riderId: $id, suspension_id: $request->suspension_id);
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
            $validatedData = $request->validate();
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->riderService->suspendTemporarily(riderId: $id, suspension_id: $request->suspension_id, suspended_until: $request->suspended_until);
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

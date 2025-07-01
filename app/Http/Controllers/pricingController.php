<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\PricingRequest;
use App\Http\Resources\PricingResource;
use App\Models\pricing;
use App\Services\PricingService;
use Exception;
use Illuminate\Http\Request;

class pricingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $pricings = $this->pricingService->searchPricings(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $pricings,
                PricingResource::class,
                trans_fallback('messages.pricing.list', 'Pricing retrieved successfully')
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
                'is_active',
            ]);
            $pricings = $this->pricingService->searchPricings(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $pricings,
                PricingResource::class, // Add your resource class
                trans_fallback('messages.pricing.list', 'Pricing retrieved successfully'),
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
    public function store(PricingRequest $request)
    {
        try {
            $data = $request->validate();
            $pricing = $this->pricingService->create($data);
            return ApiResponse::sendResponseSuccess(
                $pricing,
                PricingResource::class,
                trans_fallback('messages.pricing.created', 'Pricing retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Pricing Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $pricing = $this->pricingService->findById($id);
            return ApiResponse::sendResponseSuccess(data: new PricingResource($pricing), message: trans_fallback('messages.pricing.retrieved', 'Pricing Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Pricing not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PricingRequest $request, string $id)
    {
        try {
            $pricing = $this->pricingService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new PricingResource($pricing),
                trans_fallback('messages.pricing.updated', 'Pricing updated successfully')
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
            $this->pricingService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.pricing.deleted', 'Pricing updated successfully')
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
            /** @var Coupon $pricing */ // Add PHPDoc type hint
            $pricing = $this->pricingService->findById($id);
            $pricing->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.pricing.deactivated', 'Pricing DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.pricing.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function Activate($id)
    {
        try {
            /** @var Coupon $pricing */ // Add PHPDoc type hint
            $pricing = $this->pricingService->findById($id);
            $pricing->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.pricing.activated', 'Pricing Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.pricing.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}

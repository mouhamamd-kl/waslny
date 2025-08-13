<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Country\PaymentMethodAdminSearchRequest;
use App\Http\Requests\Country\PaymentMethodRiderSearchRequest;
use App\Http\Requests\PaymentMethod\PaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    protected $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }
    /**
     * Display a listing of the resource.
     */
    public function riderIndex(Request $request)
    {
        try {
            $payment_methods = $this->paymentMethodService->searchPaymentMethods(
                $request->input('filters',  ['is_active' => true]),
                $request->input('per_page', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $payment_methods,
                PaymentMethodResource::class,
                trans_fallback('messages.payment_method.list', 'Payment Methods retrieved successfully')
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
    public function adminIndex(Request $request)
    {
        try {
            $filters = [];
            $payment_methods = $this->paymentMethodService->searchPaymentMethods(
                $request->input('filters', $filters),
                $request->input('per_page', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $payment_methods,
                PaymentMethodResource::class,
                trans_fallback('messages.payment_method.list', 'Payment Methods retrieved successfully')
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
    public function riderSearch(PaymentMethodRiderSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $payment_methods = $this->paymentMethodService->searchPaymentMethods(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $payment_methods,
                PaymentMethodResource::class, // Add your resource class
                trans_fallback('messages.payment_method.list', 'Payment Methods retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function adminSearch(PaymentMethodAdminSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $payment_methods = $this->paymentMethodService->searchPaymentMethods(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $payment_methods,
                PaymentMethodResource::class, // Add your resource class
                trans_fallback('messages.payment_method.list', 'Payment Methods retrieved successfully'),
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
    public function store(PaymentMethodRequest $request)
    {
        try {
            $data = $request->validate();
            $payment_method = $this->paymentMethodService->create($data);
            return ApiResponse::sendResponseSuccess(
                $payment_method,
                PaymentMethodResource::class,
                trans_fallback('messages.payment_method.created', 'Payment Methods retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Payment Method Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $payment_method = $this->paymentMethodService->findById($id);
            if (!$payment_method) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new PaymentMethodResource($payment_method), message: trans_fallback('messages.payment_method.retrieved', 'Payment Method Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentMethodRequest $request, string $id)
    {
        try {
            $payment_method = $this->paymentMethodService->findById($id);
            if (!$payment_method) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
            }
            $payment_method = $this->paymentMethodService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new PaymentMethodResource($payment_method),
                trans_fallback('messages.payment_method.updated', 'Payment Method updated successfully')
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
            $payment_method = $this->paymentMethodService->findById($id);
            if (!$payment_method) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
            }
            $this->paymentMethodService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.payment_method.deleted', 'Payment Method updated successfully')
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
            /** @var Coupon $payment_method */ // Add PHPDoc type hint
            $payment_method = $this->paymentMethodService->findById($id);
            if (!$payment_method) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
            }
            $payment_method->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.payment_method.deactivated', 'Payment Method DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.payment_method.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var Coupon $payment_method */ // Add PHPDoc type hint
            $payment_method = $this->paymentMethodService->findById($id);
            if (!$payment_method) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Payment Method not found'), 404);
            }
            $payment_method->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.payment_method.activated', 'Payment Method Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.payment_method.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}

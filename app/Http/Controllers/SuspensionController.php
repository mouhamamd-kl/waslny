<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Suspension\SuspensionRequest;
use App\Http\Requests\Suspension\SuspensionSearchRequest;
use App\Http\Resources\SuspensionResource;
use App\Models\Suspension;
use App\Services\SuspensionService;
use Exception;
use Illuminate\Http\Request;

class SuspensionController extends Controller
{
    protected $suspensionService;

    public function __construct(SuspensionService $suspensionService)
    {
        $this->suspensionService = $suspensionService;
    }

    /**
     * Display a listing of the resource.
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $suspensions = $this->suspensionService->searchSuspension(
                filters: $request->input('filters', []),
                perPage: $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $suspensions,
                SuspensionResource::class,
                trans_fallback('messages.suspension.list', 'Suspension retrieved successfully')
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
    public function search(SuspensionSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $suspensions = $this->suspensionService->searchSuspension(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $suspensions,
                SuspensionResource::class, // Add your resource class
                trans_fallback('messages.suspension.list', 'Suspensions list retrieved successfully'),
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
    public function store(SuspensionRequest $request)
    {
        try {
            $data = $request->validated();
            $suspension = $this->suspensionService->create($data);
            return ApiResponse::sendResponseSuccess(
                new SuspensionResource($suspension),
                trans_fallback('messages.suspension.created', 'Suspension created successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Suspension Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $suspension = $this->suspensionService->findById($id);
            if (!$suspension) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new SuspensionResource($suspension), message: trans_fallback('messages.suspension.retrieved', 'Suspension Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SuspensionRequest $request, string $id)
    {
        try {
            $suspension = $this->suspensionService->findById($id);
            if (!$suspension) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
            }
            $suspension = $this->suspensionService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new SuspensionResource($suspension),
                trans_fallback('messages.suspension.updated', 'Suspension updated successfully')
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
            /** @var Suspension $suspension */ // Add PHPDoc type hint
            $suspension = $this->suspensionService->findById($id);
            if (!$suspension) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
            }
            if ($suspension->is_system()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.suspension.error.system_suspension_delete_failed', ['reason' => $suspension->reason]),
                    403
                );
            }
            $this->suspensionService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.suspension.deleted', 'Suspension updated successfully')
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
            /** @var Suspension $suspension */ // Add PHPDoc type hint
            $suspension = $this->suspensionService->findById($id);
            if (!$suspension) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
            }
            if ($suspension->is_system()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.suspension.error.system_suspension_deactivation_failed', ['reason' => $suspension->reason]),
                    403
                );
            }
            $suspension->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.suspension.deactivated', 'Suspension deactivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.suspension.error.deactivation_failed', 'Deactivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var Suspension $suspension */ // Add PHPDoc type hint
            $suspension = $this->suspensionService->findById($id);
            if (!$suspension) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Suspension not found'), 404);
            }
            if ($suspension->is_system()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.suspension.error.system_suspension_activation_failed', ['reason' => $suspension->reason]),
                    403
                );
            }
            $suspension->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.suspension.activated', 'Suspension activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.suspension.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}

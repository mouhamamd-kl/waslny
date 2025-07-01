<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TripTypeRequest;
use App\Http\Resources\TripTypeResource;
use App\Models\TripType;
use App\Services\TripTypeService;
use Exception;
use Illuminate\Http\Request;

class TripTypeController extends Controller
{
    protected $tripTypeService;

    public function __construct(TripTypeService $tripTypeService)
    {
        $this->tripTypeService = $tripTypeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $trip_types = $this->tripTypeService->searchTripType(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $trip_types,
                TripTypeResource::class,
                trans_fallback('messages.trip_type.list', 'Trip Types retrieved successfully')
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
            $trip_types = $this->tripTypeService->searchTripType(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $trip_types,
                TripTypeResource::class, // Add your resource class
                trans_fallback('messages.trip_type.list', 'Trip Types retrieved successfully'),
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
    public function store(TripTypeRequest $request)
    {
        try {
            $data = $request->validate();
            $trip_type = $this->tripTypeService->create($data);
            return ApiResponse::sendResponseSuccess(
                $trip_type,
                TripTypeResource::class,
                trans_fallback('messages.trip_type.created', 'Trip Types retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'trip_type Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $trip_type = $this->tripTypeService->findById($id);
            return ApiResponse::sendResponseSuccess(data: new TripTypeResource($trip_type), message: trans_fallback('messages.trip_type.retrieved', 'trip_type Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripTypeRequest $request, string $id)
    {
        try {
            $trip_type = $this->tripTypeService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new TripTypeResource($trip_type),
                trans_fallback('messages.trip_type.updated', 'trip_type updated successfully')
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
            $this->tripTypeService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_type.deleted', 'trip_type updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var TripType $triptype */ // Add PHPDoc type hint
            $triptype = $this->tripTypeService->activate($id);
            $triptype->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_type.activated', 'Trip Type Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_type.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
    public function deActivate($id)
    {
        try {
            /** @var TripType $triptype */ // Add PHPDoc type hint
            $triptype = $this->tripTypeService->findById($id);
            $triptype->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_type.deactivated', 'Trip Type DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_type.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }
}

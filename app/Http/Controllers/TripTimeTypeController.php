<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TripTimeTypeRequest;
use App\Http\Requests\TripTimeTypeSearchRequest;
use App\Http\Resources\TripTimeTypeResource;
use App\Models\TripTimeType;
use App\Services\TripTimeTypeService;
use Database\Seeders\domains\trips\trip_time_types\TripTimeTypeSeeder;
use Exception;
use Illuminate\Http\Request;

class TripTimeTypeController extends Controller
{
    protected $tripTimeTypeService;

    public function __construct(TripTimeTypeService $tripTimeTypeService)
    {
        $this->tripTimeTypeService = $tripTimeTypeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $trip_time_types = $this->tripTimeTypeService->searchTripTimeType(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $trip_time_types,
                TripTimeTypeResource::class,
                trans_fallback('messages.trip_time_type.list', 'Trip Time Types retrieved successfully')
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
    public function search(TripTimeTypeSearchRequest $request)
    {
        try {
            $filters = $request->validated();
            $trip_time_types = $this->tripTimeTypeService->searchTripTimeType(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $trip_time_types,
                TripTimeTypeResource::class, // Add your resource class
                trans_fallback('messages.trip_time_type.list', 'Trip Time Types retrieved successfully'),
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
    public function store(TripTimeTypeRequest $request)
    {
        try {
            $data = $request->validate();
            $trip_time_type = $this->tripTimeTypeService->create($data);
            return ApiResponse::sendResponseSuccess(
                $trip_time_type,
                TripTimeTypeResource::class,
                trans_fallback('messages.trip_time_type.created', 'Trip Time Types retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'trip_time_type Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $trip_time_type = $this->tripTimeTypeService->findById($id);
            return ApiResponse::sendResponseSuccess(data: new TripTimeTypeResource($trip_time_type), message: trans_fallback('messages.trip_time_type.retrieved', 'trip_time_type Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_time_type not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripTimeTypeRequest $request, string $id)
    {
        try {
            $trip_time_type = $this->tripTimeTypeService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new TripTimeTypeResource($trip_time_type),
                trans_fallback('messages.trip_time_type.updated', 'trip_time_type updated successfully')
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
            $this->tripTimeTypeService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_time_type.deleted', 'trip_time_type updated successfully')
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
            $triptype = $this->tripTimeTypeService->activate($id);
            $triptype->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_time_type.activated', 'Trip Time Type Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_time_type.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
    public function deActivate($id)
    {
        try {
            /** @var TripType $triptype */ // Add PHPDoc type hint
            $triptype = $this->tripTimeTypeService->findById($id);
            $triptype->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_time_type.deactivated', 'Trip Time Type DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_time_type.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }
}

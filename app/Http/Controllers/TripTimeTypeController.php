<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TripTimeType\TripTimeTypeAdminSearchRequest;
use App\Http\Requests\TripTimeType\TripTimeTypeStoreRequest;
use App\Http\Requests\TripTimeType\TripTimeTypeUpdateRequest;
use App\Http\Requests\TripTimeType\TripTimeTypeRiderSearchRequest;
use App\Http\Requests\TripTimeType\TripTimeTypeSearchRequest;
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
                $request->input('per_page', 5)
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

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function search(TripTimeTypeSearchRequest $request)
    // {
    //     try {
    //         $filters = array_filter($request->validated(), fn($value) => !is_null($value));
    //         $trip_time_types = $this->tripTimeTypeService->searchTripTimeType(
    //             filters: $filters,
    //             perPage: $request->input('per_page', 10),
    //         );

    //         return ApiResponse::sendResponsePaginated(
    //             $trip_time_types,
    //             TripTimeTypeResource::class, // Add your resource class
    //             trans_fallback('messages.trip_time_type.list', 'Trip Time Types retrieved successfully'),
    //         );
    //     } catch (Exception $e) {
    //         return ApiResponse::sendResponseError(
    //             'Search failed: ' . $e->getMessage(),
    //             500
    //         );
    //     }
    // }

    /**
     * Display a listing of the resource.
     */
    public function adminSearch(TripTimeTypeAdminSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
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
     * Display a listing of the resource.
     */
    public function riderSearch(TripTimeTypeRiderSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $filters['is_active'] = true;
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
    public function store(TripTimeTypeStoreRequest $request)
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
            if (!$trip_time_type) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_time_type not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new TripTimeTypeResource($trip_time_type), message: trans_fallback('messages.trip_time_type.retrieved', 'trip_time_type Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_time_type not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripTimeTypeUpdateRequest $request, string $id)
    {
        try {
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $trip_time_type = $this->tripTimeTypeService->update((int) $id, $data);
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
            /** @var TripTimeType $tripTimeType */ // Add PHPDoc type hint
            $tripTimeType = $this->tripTimeTypeService->findById($id);
            if (!$tripTimeType) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_time_type not found'), 404);
            }
            $tripTimeType->activate();
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
            /** @var TripTimeType $tripTimeType */ // Add PHPDoc type hint
            $tripTimeType = $this->tripTimeTypeService->findById($id);
            if (!$tripTimeType) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_time_type not found'), 404);
            }
            $tripTimeType->deactivate();
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

<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TripType\StoreTripTypeRequest;
use App\Http\Requests\TripType\TripTypeAdminSearchRequest;
use App\Http\Requests\TripType\UpdateTripTypeRequest;
use App\Http\Requests\TripType\TripTypeRiderSearchRequest;
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
    public function adminIndex(Request $request)
    {
        try {
            $trip_types = $this->tripTypeService->searchTripType(
                $request->input('filters', []),
                $request->input('per_page', 10)
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
    public function riderIndex(Request $request)
    {
        try {
            $trip_types = $this->tripTypeService->searchTripType(
                $request->input('filters', ['is_active' => true]),
                $request->input('per_page', 10)
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

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function search(TripTypeAdminSearchRequest $request)
    // {
    //     try {
    //         $filters = array_filter($request->validated(), fn($value) => !is_null($value));
    //         $trip_types = $this->tripTypeService->searchTripType(
    //             filters: $filters,
    //             perPage: $request->input('per_page', 10),
    //         );

    //         return ApiResponse::sendResponsePaginated(
    //             $trip_types,
    //             TripTypeResource::class, // Add your resource class
    //             trans_fallback('messages.trip_type.list', 'Trip Types retrieved successfully'),
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
    public function adminSearch(TripTypeAdminSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
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
     * Display a listing of the resource.
     */
    public function riderSearch(TripTypeRiderSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $filters['is_active'] = true;
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
    public function store(StoreTripTypeRequest $request)
    {
        try {
            $data = $request->validated();
            $trip_type = $this->tripTypeService->create($data);
            return ApiResponse::sendResponseSuccess(
                new TripTypeResource($trip_type),
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
            if (!$trip_type) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new TripTypeResource($trip_type), message: trans_fallback('messages.trip_type.retrieved', 'trip_type Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripTypeRequest $request, string $id)
    {
        try {
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $trip_type = $this->tripTypeService->update((int) $id, $data);
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
            /** @var TripType $tripType */ // Add PHPDoc type hint
            $tripType = $this->tripTypeService->findById($id);
            if (!$tripType) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
            }
            if ($tripType->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_type.error.system_trip_type_delete_failed', ['name' => $tripType->name]),
                    403
                );
            }
            if ($tripType->trips()->exists()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_type.error.has_trips', 'Cannot delete a trip type that is associated with trips.'),
                    409
                );
            }
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
            $triptype = $this->tripTypeService->findById($id);
            if (!$triptype) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
            }
            if ($triptype->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_type.error.system_trip_type_activation_failed', ['name' => $triptype->name]),
                    403
                );
            }
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
            if (!$triptype) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_type not found'), 404);
            }
            if ($triptype->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_type.error.system_trip_type_deactivation_failed', ['name' => $triptype->name]),
                    403
                );
            }
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

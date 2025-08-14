<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TripStatus\TripStatusSearchRequest;
use App\Http\Requests\TripStatus\TripStatusStoreRequest;
use App\Http\Requests\TripStatus\TripStatusUpdateRequest;
use App\Http\Resources\TripStatusResource;
use App\Models\TripStatus;
use App\Services\TripStatusService;
use Exception;
use Illuminate\Http\Request;

class TripStatusController extends Controller
{
    protected $tripStatusService;

    public function __construct(TripStatusService $tripStatusService)
    {
        $this->tripStatusService = $tripStatusService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $trip_statuses = $this->tripStatusService->searchTripStatus(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $trip_statuses,
                TripStatusResource::class,
                trans_fallback('messages.trip_status.list', 'Trip Statuses retrieved successfully')
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
    public function search(TripStatusSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $trip_statuses = $this->tripStatusService->searchTripStatus(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $trip_statuses,
                TripStatusResource::class, // Add your resource class
                trans_fallback('messages.trip_status.list', 'Trip Statuses retrieved successfully'),
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
    public function store(TripStatusStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $trip_status = $this->tripStatusService->create($data);
            return ApiResponse::sendResponseSuccess(
                new TripStatusResource($trip_status),
                trans_fallback('messages.trip_status.created', 'Trip Statuses retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Trip Status Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $trip_status = $this->tripStatusService->findById($id);
            if (!$trip_status) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip Status not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new TripStatusResource($trip_status), message: trans_fallback('messages.trip_status.retrieved', 'Trip Status Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip Status not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripStatusUpdateRequest $request, string $id)
    {
        try {
            $trip_status = $this->tripStatusService->findById($id);
            if (!$trip_status) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip Status not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $trip_status = $this->tripStatusService->update((int) $id, $data);
            return ApiResponse::sendResponseSuccess(
                new TripStatusResource($trip_status),
                trans_fallback('messages.trip_status.updated', 'Trip Status updated successfully')
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
            /** @var TripStatus $tripStatus */ // Add PHPDoc type hint
            $tripStatus = $this->tripStatusService->findById($id);
            if (!$tripStatus) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Trip Status not found'), 404);
            }
            if ($tripStatus->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_status.error.system_trip_status_delete_failed', ['name' => $tripStatus->name]),
                    403
                );
            }
            $this->tripStatusService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_status.deleted', 'Trip Status updated successfully')
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
            /** @var TripStatus $tripStatus */ // Add PHPDoc type hint
            $tripStatus = $this->tripStatusService->findById($id);
            if (!$tripStatus) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_status not found'), 404);
            }
            if ($tripStatus->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_status.error.system_trip_status_activation_failed', ['name' => $tripStatus->name]),
                    403
                );
            }
            $tripStatus->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_status.activated', 'Trip Status Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_status.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
    public function deActivate($id)
    {
        try {
            /** @var TripStatus $tripStatus */ // Add PHPDoc type hint
            $tripStatus = $this->tripStatusService->findById($id);
            if (!$tripStatus) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'trip_status not found'), 404);
            }
            if ($tripStatus->is_system_defined) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.trip_status.error.system_trip_status_deactivation_failed', ['name' => $tripStatus->name]),
                    403
                );
            }
            $tripStatus->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.trip_status.deactivated', 'Trip Status DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.trip_status.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\DriverStatusRequest;
use App\Http\Resources\DriverStatusResource;
use App\Models\DriverStatus;
use App\Services\DriverStatusService;
use Exception;
use Illuminate\Http\Request;

class DriverStatusController extends Controller
{
    protected $driverStatusService;

    public function __construct(DriverStatusService $driverStatusService)
    {
        $this->driverStatusService = $driverStatusService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $driver_statuses = $this->driverStatusService->searchDriverStatus(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $driver_statuses,
                DriverStatusResource::class,
                trans_fallback('messages.driver_status.list', 'Driver Statuses retrieved successfully')
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
            $driver_statuses = $this->driverStatusService->searchDriverStatus(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $driver_statuses,
                DriverStatusResource::class, // Add your resource class
                trans_fallback('messages.driver_status.list', 'Driver Statuses retrieved successfully'),
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
    public function store(DriverStatusRequest $request)
    {
        try {
            $data = $request->validate();
            $driver_status = $this->driverStatusService->create($data);
            return ApiResponse::sendResponseSuccess(
                $driver_status,
                DriverStatusResource::class,
                trans_fallback('messages.driver_status.created', 'Driver Statuses Created successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Driver Status Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $driver_status = $this->driverStatusService->findById($id);
            if (!$driver_status) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver Status not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new DriverStatusResource($driver_status), message: trans_fallback('messages.driver_status.retrieved', 'Driver Status Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver Status not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DriverStatusRequest $request, string $id)
    {
        try {
            $driver_status = $this->driverStatusService->findById($id);
            if (!$driver_status) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver Status not found'), 404);
            }
            $driver_status = $this->driverStatusService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new DriverStatusResource($driver_status),
                trans_fallback('messages.driver_status.updated', 'Driver Status updated successfully')
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
            $driver_status = $this->driverStatusService->findById($id);
            if (!$driver_status) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver Status not found'), 404);
            }
            $this->driverStatusService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.driver_status.deleted', 'Driver Status deleted successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }
}

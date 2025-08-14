<?php

namespace App\Http\Controllers;

use App\Enums\DriverStatusEnum;
use App\Enums\SuspensionReason;
use App\Helpers\ApiResponse;
use App\Http\Requests\Driver\DriverSearchRequest;
use App\Http\Requests\Driver\DriverUpdateLocationRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Events\DriverLocationUpdatedForDashboard;
use App\Http\Requests\Suspension\SuspendAccountForeverRequest;
use App\Http\Requests\Suspension\SuspendAccountTemporarilyRequest;
use App\Services\DriverService;
use Exception;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $drivers = $this->driverService->searchDrivers(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $drivers,
                DriverResource::class,
                trans_fallback('messages.driver.list', 'Driver  retrieved successfully')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function search(DriverSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $drivers = $this->driverService->searchDrivers(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $drivers,
                DriverResource::class, // Add your resource class
                trans_fallback('messages.driver.list', 'Driver  retrieved successfully'),
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
    public function show($id)
    {
        try {
            $driver = $this->driverService->findById($id);
            if (!$driver) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new DriverResource($driver), message: trans_fallback('messages.driver.retrieved', 'Driver  Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver  not found'), 404);
        }
    }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(UpdateDriverProfileRequest $request, string $id)
    // {
    //     try {
    //         $driver = $this->driverService->update((int) $id, $request->validated());
    //         return ApiResponse::sendResponseSuccess(
    //             new DriverResource($driver),
    //             trans_fallback('messages.driver.updated', 'Driver  updated successfully')
    //         );
    //     } catch (Exception $e) {
    //         return ApiResponse::sendResponseError(
    //             trans_fallback('messages.error.update_failed', 'Update failed: ' . $e->getMessage())
    //         );
    //     }
    // }

    public function suspendForever($id, SuspendAccountForeverRequest $request)
    {
        try {
            $driver = $this->driverService->findById($id);
            if (!$driver) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver not found'), 404);
            }
            $validatedData = $request->validated();
            /** @var Driver $driver */ // Add PHPDoc type hint
            $this->driverService->suspendForever(driverId: $id, suspension_id: $request->suspension_id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.driver.suspended', 'Driver Suspended successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.suspension_failed', 'Suspension failed: ' . $e->getMessage())
            );
        }
    }
    public function suspendTemporarily($id, SuspendAccountTemporarilyRequest $request)
    {
        try {
            $driver = $this->driverService->findById($id);
            if (!$driver) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver not found'), 404);
            }
            $validatedData = $request->validated();
            /** @var Driver $driver */ // Add PHPDoc type hint
            $this->driverService->suspendTemporarily(driverId: $id, suspension_id: $request->suspension_id, suspended_until: $request->suspended_until);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.driver.suspended', 'Driver Suspended successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.suspension_failed', 'Suspension failed: ' . $e->getMessage())
            );
        }
    }

    public function updateLocation(DriverUpdateLocationRequest $request)
    {
        $validatedData = $request->validated();
        $location = $validatedData['location'];
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        try {
            if (!$driver) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.error.not_found', 'Driver not found')
                );
            }
            $driver->setLocation($location);

            event(new DriverLocationUpdatedForDashboard($driver->id, $location));


            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.driver.location_updated', 'Location updated successfully.')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.location_update_failed', 'Failed to update location: ' . $e->getMessage())
            );
        }
    }

    public function SwitchToOnlineStatus(Request $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        try {
            if (!$driver) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.error.not_found', 'Driver not found')
                );
            }
            $driver->setStatus(DriverStatusEnum::STATUS_AVAILABLE);
            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.driver.status_online', 'Status updated to online.')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.status_update_failed', 'Failed to update status: ' . $e->getMessage())
            );
        }
    }
    public function SwitchToOfflineStatus(Request $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        try {
            if (!$driver) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.error.not_found', 'Driver not found')
                );
            }
            $driver->setStatus(DriverStatusEnum::STATUS_OFFLINE);
            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.driver.status_offline', 'Status updated to offline.')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.status_update_failed', 'Failed to update status: ' . $e->getMessage())
            );
        }
    }

    public function reinstate($id)
    {
        try {
            $driver = $this->driverService->findById($id);
            if (!$driver) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Driver not found'), 404);
            }
            /** @var Driver $driver */ // Add PHPDoc type hint
            $this->driverService->activate($id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.driver.reinstate', 'Driver Reinstate successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.reinstate_failed', 'Re Instate failed: ' . $e->getMessage())
            );
        }
    }
}

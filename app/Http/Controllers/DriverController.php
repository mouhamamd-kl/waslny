<?php

namespace App\Http\Controllers;

use App\Enums\SuspensionReason;
use App\Helpers\ApiResponse;
use App\Http\Requests\DriverUpdateRequest;
use App\Http\Requests\SuspendAccountForeverRequest;
use App\Http\Requests\SuspendAccountRequest;
use App\Http\Requests\DriverSearchRequest;
use App\Http\Requests\SuspendAccountTemporarilyRequest;
use App\Http\Requests\UpdateDriverProfileRequest;
use Illuminate\Http\UploadedFile;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
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
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $drivers,
                DriverResource::class,
                trans_fallback('messages.driver.list', 'Driver  retrieved successfully')
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
    public function search(DriverSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn ($value) => !is_null($value));
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

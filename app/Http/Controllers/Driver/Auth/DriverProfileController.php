<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverCarCompleteRequest;
use App\Http\Requests\DriverCompleteProfileRequest;
use App\Http\Requests\RiderCompleteProfileRequest;
use App\Http\Requests\UpdateAgentProfileRequest;
use App\Models\Agent;
use App\Models\DriverCar;
use App\Services\AssetsService;
use App\Services\DriverCarService;
use Exception;

class DriverProfileController extends Controller
{
    protected DriverCarService $driverservice;
    // app/Http/Controllers/Api/Agent/ProfileController.php
    public function completeProfile(DriverCompleteProfileRequest $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        // Handle paperwork file upload
        try {
            $data = $request->validate();
            $driver->update(attributes: $data);
            return ApiResponse::sendResponseSuccess(
                $driver->fresh(),
                trans_fallback('messages.driver.completion_success', 'Driver Profile Created successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.creation_failed', 'Creation failed: ') . $e->getMessage()
            );
        }
    }

    public function completeDriverCar(DriverCarCompleteRequest $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        // Handle paperwork file upload
        try {
            $data = $request->validate();
            $data = array_merge($data, [
                'driver_id' => $driver->id,
            ]);
            /** @var DriverCar $driverCar */ // Add PHPDoc type hint
            $driverCar = $this->driverservice->create($data);
            return ApiResponse::sendResponseSuccess(
                $driverCar,
                trans_fallback('messages.driver.car.created', 'Driver Car Created successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.creation_failed', 'Creation failed: ') . $e->getMessage()
            );
        }
    }
}

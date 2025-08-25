<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\Auth\DriverCarCompleteRequest;
use App\Http\Requests\Driver\Auth\DriverCompleteProfileRequest;
use App\Http\Requests\RiderCompleteProfileRequest;
use App\Http\Requests\UpdateAgentProfileRequest;
use App\Http\Requests\Driver\UpdateDriverProfileRequest;
use App\Http\Resources\DriverCarResource;
use App\Http\Resources\DriverResource;
use App\Http\Resources\RiderResource;
use App\Models\Agent;
use App\Models\CarPhotoType;
use App\Models\DriverCar;
use App\Models\DriverPhotoType;
use App\Services\AssetsService;
use App\Services\DriverCarService;
use App\Services\DriverService;
use App\Services\FileServiceFactory;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class DriverProfileController extends Controller
{
    protected DriverCarService $driverCarservice;
    protected DriverService $driverservice;

    public function __construct(DriverCarService $driverCarService, DriverService $driverService)
    {
        $this->driverCarservice = $driverCarService;
        $this->driverservice = $driverService;
    }

    public function profile(Request $request)
    {
        try {
            $user = auth('driver-api')->user();

            if (!$user) {
                return ApiResponse::sendResponseError(
                    null,
                    trans_fallback('messages.auth.unauthenticated', 'Unauthenticated'),
                    401,
                );
            }

            return ApiResponse::sendResponseSuccess(
                new DriverResource($user),
                trans_fallback('messages.auth.profile.retrieved', 'Profile retrieved successfully'),
                200
            );
        } catch (Exception $e) {
            // In your try-catch blocks:
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    // app/Http/Controllers/Api/Agent/ProfileController.php
    public function completeProfile(DriverCompleteProfileRequest $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        // Handle paperwork file upload
        try {
            if (!$driver->isProfileComplete()) {
                $data = $request->validated();

                foreach (DriverPhotoType::cases() as $type) {
                    unset($data[$type->value . '_photo']);
                    if ($request[$type->value . '_photo']) {
                        $file = $request[$type->value . '_photo'];

                        if ($file instanceof UploadedFile) {
                            // $driverCar->updatePhoto($type, $file);
                            $driver->updatePhoto($type, $file);
                        }
                    }
                }
                $driver->update(attributes: $data);
                return ApiResponse::sendResponseSuccess(
                    new DriverResource($driver->fresh()),
                    trans_fallback('messages.driver.completion_success', 'Driver Profile Created successfully')
                );
            }
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.profile_already_completed', 'Driver Profile Already Completed'),
                409
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function completeDriverCar(DriverCarCompleteRequest $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = auth('driver-api')->user();
        // Handle paperwork file upload
        try {
            if (!$driver->isDriverCarComplete()) {
                $data = $request->validated();
                $data = array_merge($data, [
                    'driver_id' => $driver->id,
                ]);
                $assetService = FileServiceFactory::makeForDriverCarPhotos();
                foreach (CarPhotoType::cases() as $type) {
                    $file = $request[$type->value . '_photo'];
                    if ($file instanceof UploadedFile) {
                        // $driverCar->updatePhoto($type, $file);
                        $path = "{$driver->id}";
                        $data[$type->value . '_photo'] =  $assetService->uploadPublic($data['inside_photo'], $path);
                    }
                }
                /** @var DriverCar $driverCar */ // Add PHPDoc type hint
                $driverCar = $this->driverCarservice->create($data);
                $this->driverservice->suspendNeedConfirmation($driver->id);
                return ApiResponse::sendResponseSuccess(
                    new  DriverCarResource($driverCar),
                    trans_fallback('messages.driver.car.created', 'Driver Car Created successfully')
                );
            }
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.error.car_already_added', 'Driver Car Already Completed'),
                409
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function updateProfile(UpdateDriverProfileRequest $request)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = auth('driver-api')->user();
            $data = $request->validated();

            // Remove fields we don't want to update directly
            unset($data['profile_photo']);
            unset($data['driver_license_photo']);


            foreach (DriverPhotoType::cases() as $type) {
                if ($request[$type->value . '_photo']) {

                    $file = $request[$type->value . '_photo'];
                    if ($file instanceof UploadedFile) {
                        // $driverCar->updatePhoto($type, $file);
                        $driver->updatePhoto($type, $file);
                    }
                }
            }

            $driver->update($data);


            return ApiResponse::sendResponseSuccess(
                new DriverResource($driver),
                trans_fallback('messages.auth.profile.updated', 'Profile updated successfully')
            );
        } catch (\Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'Failed to update profile'),
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}

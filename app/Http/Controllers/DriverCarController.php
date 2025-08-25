<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\DriverCar\UpdateDriverCarRequest;
use App\Http\Resources\DriverCarResource;
use App\Models\CarPhotoType;
use App\Models\DriverCar;
use App\Services\DriverCarService;
use App\Services\DriverService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class DriverCarController extends Controller
{
    protected DriverCarService $driverCarservice;
    protected DriverService $driverservice;
    public function __construct(DriverCarService $driverCarService, DriverService $driverService)
    {
        $this->driverCarservice = $driverCarService;
        $this->driverservice = $driverService;
    }

    public function index(Request $request)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = auth('driver-api')->user();
            /** @var DriverCar $driverCar */ // Add PHPDoc type hint
            $driverCar = $driver->driverCar;
            return ApiResponse::sendResponseSuccess(
                new DriverCarResource($driverCar),
                trans_fallback('messages.driver.car.retrieved', 'Car data retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.car.error.retrieval_failed', 'Failed to retrieve car data'),
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function update(UpdateDriverCarRequest $request)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = auth('driver-api')->user();
            $data = $request->validated();
            /** @var DriverCar $driverCar */ // Add PHPDoc type hint
            $driverCar = $driver->driverCar;
            foreach (CarPhotoType::cases() as $type) {
                if ($request[$type->value . '_photo']) {
                    unset($data[$type->value . '_photo']);
                    $file = $request[$type->value . '_photo'];
                    if ($file instanceof UploadedFile) {
                        // $driverCar->updatePhoto($type, $file);
                        $driverCar->updatePhoto($type, $file);
                    }
                }
            }
            $driverCar->update($data);
            $this->driverservice->suspendNeedConfirmation($driver->id);
            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.driver.car.updated', 'Car data updated successfully')
            );
        } catch (\Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.car.error.update_failed', 'Failed to update car data'),
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}

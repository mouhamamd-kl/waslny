<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\DriverCar\UpdateDriverCarRequest;
use App\Http\Resources\DriverCarResource;
use App\Models\CarPhotoType;
use App\Models\DriverCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class DriverCarController extends Controller
{

    public function update(UpdateDriverCarRequest $request)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = auth('driver-api')->user();
            $data = $request->validated();

            foreach (CarPhotoType::cases() as $type) {
                if ($request[$type->value . '_photo']) {

                    $file = $request[$type->value . '_photo'];
                    if ($file instanceof UploadedFile) {
                        // $driverCar->updatePhoto($type, $file);
                        $driver->updatePhoto($type, $file);
                    }
                }
            }

            return ApiResponse::sendResponseSuccess(
                new DriverCarResource($driver),
                trans_fallback('messages.auth.profile.updated', 'Profile updated successfully')
            );
        } catch (\Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'Failed to update profile'),
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}

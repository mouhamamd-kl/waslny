<?php

namespace App\Http\Controllers\Rider\Auth;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RiderCompleteProfileRequest;
use App\Http\Requests\UpdateRiderProfileRequest;
use App\Http\Resources\RiderResource;
use App\Models\RiderPhotoType;
use App\Services\RiderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Exception;

class RiderProfileController extends Controller
{
    protected $riderService;

    public function __construct(RiderService $riderService)
    {
        $this->riderService = $riderService;
    }

    public function profile(Request $request)
    {
        // $user = auth('rider-api')->user();
        // if ($user) {
        //     return ApiResponse::sendResponseSuccess(
        //         new RiderResource($user),
        //         trans_fallback('messages.auth.profile.retrieved', 'Profile retrieved successfully'),
        //     );
        // }
        try {
            $user = auth('rider-api')->user();

            if (!$user) {
                return ApiResponse::sendResponseError(
                    null,
                    trans_fallback('messages.auth.unauthenticated', 'Unauthenticated'),
                    401,
                );
            }

            return ApiResponse::sendResponseSuccess(
                new RiderResource($user),
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
    public function completeProfile(RiderCompleteProfileRequest $request)
    {
        /** @var Rider $rider */ // Add PHPDoc type hint
        $rider = auth('rider-api')->user();
        // Handle paperwork file upload
        try {
            DB::beginTransaction();
            if (!$rider->isProfileComplete()) {
                $validated = $request->validated();
                
                $rider->update($validated);
                return ApiResponse::sendResponseSuccess(
                    $rider->fresh(),
                    trans_fallback('messages.rider.completion_success', 'Rider Profile Completion Success')
                );
            }
            DB::commit(); // Never reached
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.error.profile_already_completed', 'Rider Profile Already Completed'),
                409
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.creation_failed', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function updateProfile(UpdateRiderProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = auth('rider-api')->user();
            $data = $request->validated();

            // Remove fields we don't want to update directly
            unset($data['profile_photo']);

            foreach (RiderPhotoType::cases() as $type) {
                if ($request[$type->value . '_photo']) {
                    $file = $request[$type->value . '_photo'];
                    if ($file instanceof UploadedFile) {
                        // $driverCar->updatePhoto($type, $file);
                        $rider->updatePhoto($type, $file);
                    }
                }
            }
            $rider->update($data);
            DB::commit();
            return ApiResponse::sendResponseSuccess(
                new RiderResource($rider),
                trans_fallback('messages.auth.profile.updated', 'Profile updated successfully')
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function show(Request $request)
    {
        try {
            $rider = auth('rider-api')->user();
            return ApiResponse::sendResponseSuccess(data: new RiderResource($rider), message: trans_fallback('messages.rider.retrieved', 'Rider Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider  not found'), 404);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use App\Http\Requests\AgentRegisterRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Models\AdminPhotoType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top

class AdminProfileController extends Controller
{
    public function profile(Request $request)
    {
        // try {
        //     $user = auth('admin-api')->user();
        //     if ($user) {
        //         return ApiResponse::sendResponseSuccess(
        //             new AdminResource($user),
        //             trans_fallback('messages.auth.profile.retrieved', 'Profile retrieved successfully'),
        //         );
        //     }
        //     return ApiResponse::sendResponseError(
        //         null,
        //         trans_fallback('messages.error.generic', 'Something wrong happend'),
        //         500,
        //     );
        // } catch (Exception $e) {
        //     return ApiResponse::sendResponseError(
        //         trans_fallback('messages.error.update_failed', 'Failed to update profile'),
        //         500,
        //         ['error' => $e->getMessage()]
        //     );
        // }
        try {
            $user = auth('admin-api')->user();

            if (!$user) {
                return ApiResponse::sendResponseError(
                    null,
                    trans_fallback('messages.auth.unauthenticated', 'Unauthenticated'),
                    401,
                    ['auth' => trans_fallback('messages.auth.unauthenticated', 'Please login to continue')]
                );
            }

            return ApiResponse::sendResponseSuccess(
                new AdminResource($user),
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

    public function updateProfile(UpdateAdminProfileRequest $request)
    {
        try {
            /** @var Admin $admin */ // Add PHPDoc type hint
            $admin = auth('admin-api')->user();
            $data = $request->validated();

            // Remove fields we don't want to update directly
            unset($data['current_password']);
            unset($data['new_password']);
            unset($data['new_password_confirmation']);
            unset($data['profile_photo']);

            // Handle password change if requested
            if ($request->filled('new_password')) {
                $data['password'] = Hash::make($request->new_password);
            }

            foreach (AdminPhotoType::cases() as $type) {
                $file = $request[$type->value . '_photo'];
                if ($file instanceof UploadedFile) {
                    // $driverCar->updatePhoto($type, $file);
                    $admin->updatePhoto($type, $file);
                }
            }

            $admin->update($data);


            return ApiResponse::sendResponseSuccess(
                new AdminResource($admin),
                trans_fallback('messages.auth.profile.updated', 'Profile updated successfully')
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
}

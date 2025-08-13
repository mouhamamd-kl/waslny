<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Enums\SuspensionReason;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorCodeRequest;
use App\Http\Requests\Driver\Auth\DriverResendOtpRequest;
use App\Http\Resources\DriverResource;
use App\Http\Resources\RiderResource;
use App\Models\Agent;
use App\Models\Driver;
use App\Models\Rider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class DriverTwoFactorController extends Controller
{
    public function verify(TwoFactorCodeRequest $request)
    {
        try {
            $request->validated();
            /** @var Driver $driver */ // Add PHPDoc type hint

            $driver = Driver::where('phone', $request->phone)->first();

            if (
                ! $driver ||
                $driver->two_factor_code !== $request->otp ||
                $driver->two_factor_expires_at->lt(now())
            ) {
                return ApiResponse::sendResponseError('Invalid or expired code.', 403);
            }
            // Clear OTP after successful verification

            if ($driver->isSuspended()) {
                // Get the specific suspension message
                return ApiResponse::sendResponseError(
                    message: $driver->userSuspensionMessage(),
                    statusCode: 403,
                    data: [
                        'suspension_details' => [
                            'is_permanent' => $driver->activeSuspension()->is_permanent,
                            'suspended_until' => $driver->activeSuspension()->suspended_until,
                            'suspended_at' => $driver->activeSuspension()->created_at,
                        ]
                    ]
                );
            }
            $driver->resetTwoFactorCode();
            // Generate authentication token
            $token = $driver->createToken('driverAuthToken')->plainTextToken;
            // Determine response based on profile completion

            if ($driver->isProfileComplete() && $driver->isDriverCarComplete()) {

                return ApiResponse::sendResponseSuccess(
                    data: [
                        'token' => $token,
                        'is_new_driver' => false,
                        'next_step' => 'home',  // Added navigation target
                        'driver' => new DriverResource($driver)
                    ],
                    message: 'Login successful'
                );
            } elseif (!$driver->isProfileComplete()) {
                return ApiResponse::sendResponseSuccess(
                    data: [
                        'token' => $token,
                        'is_new_driver' => true,
                        'next_step' => 'profile_completion'  // Explicit screen target
                    ],
                    message: 'Driver profile setup required',
                );
            } elseif (!$driver->isDriverCarComplete()) {
                return ApiResponse::sendResponseSuccess(
                    data: [
                        'token' => $token,
                        'is_new_driver' => true,
                        'next_step' => 'car_completion'  // Explicit screen target
                    ],
                    message: 'Driver Car setup required',
                );
            }
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function resend(DriverResendOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $driver = Driver::where('phone', $data->phone)->first();
            $driver->generateTwoFactorCode();
            // $rider->notify(new \App\Notifications\Agent\AgentTwoFactorCode);
            return ApiResponse::sendResponseSuccess(
                ['message' => 'A new verification code has been sent to your email.'],
                'OTP code resent successfully.'
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Rider\Auth;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorCodeRequest;
use App\Http\Requests\Driver\Auth\DriverResendOtpRequest;
use App\Http\Resources\RiderResource;
use App\Models\Agent;
use App\Models\Rider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class RiderTwoFactorController extends Controller
{
    public function verify(TwoFactorCodeRequest $request)
    {
        try {
            $request->validated();
            /** @var Rider $rider */ // Add PHPDoc type hint

            $rider = Rider::where('phone', $request->phone)->first();

            if (
                ! $rider ||
                $rider->two_factor_code !== $request->otp ||
                $rider->two_factor_expires_at->lt(now())
            ) {
                return ApiResponse::sendResponseError('Invalid or expired code.', 403);
            }

            if ($rider->isSuspended()) {
                // Get the specific suspension message
                /** @var Suspension $suspension */ // Add PHPDoc type hint
                $suspension = $rider->activeSuspension()->first();

                return ApiResponse::sendResponseError(
                    message: $suspension->user_msg,
                    statusCode: 403, // Forbidden
                    data: [
                        'suspension_reason' => $suspension->reason,
                        'suspended_at' => $suspension->created_at,
                    ]
                );
            }

            // Clear OTP after successful verification
            $rider->resetTwoFactorCode();

            // Generate authentication token
            $token = $rider->createToken('riderAuthToken')->plainTextToken;



            // Determine response based on profile completion
            return $rider->isProfileComplete()
                ? ApiResponse::sendResponseSuccess(
                    data: [
                        'token' => $token,
                        'is_new_rider' => false,
                        'rider' => new RiderResource($rider)
                    ],
                    message: 'Login successful'
                )
                : ApiResponse::sendResponseSuccess(
                    data: [
                        'token' => $token,
                        'is_new_rider' => true
                    ],
                    message: 'Profile setup required',
                    statusCode: 201
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

    public function resend(DriverResendOtpRequest $request)
    {
        try {
            $data = $request->validated();
            $rider = Rider::where('phone', $data['phone'])->first();
            $rider->generateTwoFactorCode();
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

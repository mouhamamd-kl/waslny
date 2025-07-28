<?php

namespace App\Http\Controllers\AuthTest;


use App\Enums\SuspensionReason;
use App\Enums\SuspensionReasonEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RiderResource;
use App\Models\Agent;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class TwoFactorTestController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|digits:6',
        ]);
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
            $suspensionReason = SuspensionReasonEnum::tryFrom($rider->suspension_reason)
                ?? SuspensionReasonEnum::OTHER;

            return ApiResponse::sendResponseError(
                message: $suspensionReason->message(),
                statusCode: 403, // Forbidden
                data: [
                    'suspension_reason' => $rider->suspension_reason,
                    'suspended_at' => $rider->suspended_at?->toDateTimeString(),
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
    }

    public function resend(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string',],
        ], [], [
            'phone' => __('lang.phone'),
        ]);

        if ($validator->fails()) {
            return ApiResponse::sendResponseError('Validation failed', 422, $validator->errors());
        }

        $rider = Rider::where('phone', $request->phone)->first();
        $rider->generateTwoFactorCode();
        // $rider->notify(new \App\Notifications\Agent\AgentTwoFactorCode);
        return ApiResponse::sendResponseSuccess(
            ['message' => 'A new verification code has been sent to your email.'],
            'OTP code resent successfully.'
        );
    }
}

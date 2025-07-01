<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class AdminTwoFactorController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (
            ! $admin ||
            $admin->two_factor_code !== $request->otp ||
            $admin->two_factor_expires_at->lt(now())
        ) {
            return ApiResponse::sendResponseError('Invalid or expired code.', 403);
        }

        // OTP is valid — reset and return token
        $admin->resetTwoFactorCode();

        return ApiResponse::sendResponseSuccess($this->formatAdminData($admin), 'Agent logged in successfully', 200);
    }

    public function resend(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => __('lang.email'),
            'password' => __('lang.password'),
        ]);

        if ($validator->fails()) {
            return ApiResponse::sendResponseError('Validation failed', 422, $validator->errors());
        }

        $admin = Admin::where('email', $request->email)->first();
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return ApiResponse::sendResponseError('Invalids credentials', 401);
        }
        if (! $admin->two_factor_enabled) {
            return ApiResponse::sendResponseError('Two-factor authentication is not enabled for this agent.', 400);
        }

        if (! $admin->hasVerifiedEmail()) {
            return ApiResponse::sendResponseError('Email is not verified. Please verify your email first.', 403);
        }

        $admin->generateTwoFactorCode();
        $admin->notify(new \App\Notifications\Admin\AdminTwoFactorCode);

        return ApiResponse::sendResponseSuccess(
            ['message' => 'A new verification code has been sent to your email.'],
            'OTP code resent successfully.'
        );
    }

    private function formatAdminData(Admin $admin): array
    {
        return [
            'admin' => [
                'user_name' => $admin->user_name,
                'token' => $admin->createToken('agent_auth_token')->plainTextToken,
                'email' => $admin->email,
            ],
        ];
    }
}

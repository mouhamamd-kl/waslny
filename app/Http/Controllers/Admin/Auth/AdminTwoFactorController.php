<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorCodeRequest;
use App\Models\Admin;
use App\Models\Agent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class AdminTwoFactorController extends Controller
{
    public function verify(TwoFactorCodeRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->validated();

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
            DB::commit(); // Never reached
            return ApiResponse::sendResponseSuccess($this->formatAdminData($admin), 'Admin logged in successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function resend(Request $request)
    {
        try {
            DB::beginTransaction();
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

            $admin->generateTwoFactorCode();
            $admin->notify(new \App\Notifications\Admin\AdminTwoFactorCode);
            DB::commit(); // Never reached
            return ApiResponse::sendResponseSuccess(
                ['message' => 'A new verification code has been sent to your email.'],
                'OTP code resent successfully.'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
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

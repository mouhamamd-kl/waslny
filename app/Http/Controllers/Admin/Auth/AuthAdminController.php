<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRegisterRequest;
use App\Models\Admin;
use App\Models\Agent;
use App\Notifications\Admin\AdminTwoFactorCode;
use App\Notifications\Agent\AgentTwoFactorCode;
use App\Services\AssetsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class AuthAdminController extends Controller
{
    private function formatAdminData(Admin $admin): array
    {
        return [
            'admin' => [
                'user_name'=>$admin->user_name,
                'token' => $admin->createToken('agent_auth_token')->plainTextToken,
                'email' => $admin->email,
            ],
        ];
    }
    public function login(Request $request)
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

        $admin = Admin::where('contact_email', $request->contact_email)->first();
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return ApiResponse::sendResponseError('Invalids credentials', 401);
        }
        if (! $admin->hasVerifiedEmail()) {
            $admin->sendEmailVerificationNotification();

            return ApiResponse::sendResponseError(
                'Email not verified so we sent email to verify please check your inbox',
                403,
                null
            );
        }
        if ($admin->two_factor_enabled) {
            $admin->generateTwoFactorCode();
            $admin->notify(new AdminTwoFactorCode);

            return ApiResponse::sendResponseSuccess([
                'requires_otp' => true,
                'message' => 'Verification code sent to your email.',
            ]);
        }

        return ApiResponse::sendResponseSuccess($this->formatAdminData($admin), 'Admin logged in successfully', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::sendResponseSuccess([], 'Logged out successfully', 200);
    }

    // In AuthController
    public function refreshToken(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'token' => $request->user()->createToken('refresh-token')->plainTextToken,
        ]);
    }
}

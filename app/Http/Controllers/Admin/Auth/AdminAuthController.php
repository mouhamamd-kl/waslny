<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use App\Notifications\Admin\AdminTwoFactorCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
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
    public function login(AdminLoginRequest $request)
    {
        try {
            $data = $request->validated();
            $admin = Admin::where('email', $request->email)->first();
            if (! $admin || ! Hash::check($request->password, $admin->password)) {
                return ApiResponse::sendResponseError('Invalids credentials', 401);
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
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::sendResponseSuccess([], 'Logged out successfully', 200);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    // In AuthController
    public function refreshToken(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'token' => $request->user()->createToken('refresh-token')->plainTextToken,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
}

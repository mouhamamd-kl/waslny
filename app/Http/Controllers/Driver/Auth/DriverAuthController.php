<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Enums\DriverStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverLoginRequest;
use App\Models\Driver;
use App\Notifications\Driver\DriverTwoFactorCode;
use App\Services\DriverService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverAuthController extends Controller
{
    protected DriverService $driverService;
    public function login(DriverLoginRequest $request): JsonResponse
    {

        try {
         $data = $request->validated();
            // $rider = Rider::where('phone', $request->phone)->first();
            $driver = Driver::firstOrCreate(
                [
                    'phone' => $data['phone'],
                ],
            );
            $driver->setStatus(DriverStatusEnum::STATUS_OFFLINE);
            $driver->generateTwoFactorCode();
            $driver->notify(new DriverTwoFactorCode);

            return ApiResponse::sendResponseSuccess(
                [
                    'requires_otp' => true,
                ],
                trans_fallback('messages.auth.verification.sent', 'Verification Code has been sent')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.auth.logout', 'logged out successfully'),
                200
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    // In AuthController
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'token' => $request->user()->createToken('refresh-token')->plainTextToken,
            ]);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }

    public function deleteAccount(Request $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = $request->user('driver-api'); // Authenticated agent
        // return ApiResponse::sendResponseSuccess($agent, 'Agent account deleted successfully');

        try {
            // Delete uploaded files from S3
            $this->driverService->deleteAssets($driver->id);
            // Revoke tokens
            $driver->tokens()->delete();
            // Delete agent from DB
            $driver->delete();
            return ApiResponse::sendResponseSuccess(null, 'Driver account deleted successfully');
        } catch (\Throwable $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
}

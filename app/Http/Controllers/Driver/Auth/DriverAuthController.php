<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Notifications\Driver\DriverTwoFactorCode;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverAuthController extends Controller
{
    protected DriverService $driverService;
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
        ], [], [
            'phone' => __('lang.phone'),
        ]);

        if ($validator->fails()) {
            return ApiResponse::sendResponseError('Validation failed', 422, $validator->errors());
        }

        // $rider = Rider::where('phone', $request->phone)->first();
        $driver = Driver::firstOrCreate(
            [
                'phone' => $request->phone,
            ],
        );
        $driver->generateTwoFactorCode();
        $driver->notify(new DriverTwoFactorCode);
        return ApiResponse::sendResponseSuccess(
            [
                'requires_otp' => true,
            ],
            trans_fallback('messages.auth.verification.sent', 'Verification Code has been sent')
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::sendResponseSuccess(
            [],
            trans_fallback('messages.auth.logout', 'logged out successfully'),
            200
        );
    }

    // In AuthController
    public function refreshToken(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'token' => $request->user()->createToken('refresh-token')->plainTextToken,
        ]);
    }

    public function deleteAccount(Request $request)
    {
        /** @var Driver $driver */ // Add PHPDoc type hint
        $driver = $request->user('driver-api'); // Authenticated agent
        // return ApiResponse::sendResponseSuccess($agent, 'Agent account deleted successfully');

        DB::beginTransaction();
        try {
            // Delete uploaded files from S3
            $this->driverService->deleteAssets($driver->id);
            // Revoke tokens
            $driver->tokens()->delete();
            // Delete agent from DB
            $driver->delete();
            DB::commit();
            return ApiResponse::sendResponseSuccess(null, 'Driver account deleted successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError('Failed to delete account: ' . $e->getMessage(), 500);
        }
    }
}

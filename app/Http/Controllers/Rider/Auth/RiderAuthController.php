<?php

namespace App\Http\Controllers\Rider\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRegisterRequest;
use App\Http\Requests\RiderLoginRequest;
use App\Models\Agent;
use App\Models\Rider;
use App\Notifications\Agent\AgentTwoFactorCode;
use App\Notifications\Rider\RiderTwoFactorCode;
use App\Services\AssetsService;
use App\Services\FileServiceFactory;
use App\Services\RiderService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Validator;

class RiderAuthController extends Controller
{
    protected RiderService $riderSerivce;
    public function login(RiderLoginRequest $request)
    {
        try {
            $data = $request->validated();


            // $rider = Rider::where('phone', $request->phone)->first();
            $rider = Rider::firstOrCreate(
                ['phone' => $data['phone'],],
            );
            // $token = $rider->createToken('authToken')->plainTextToken;
            $rider->generateTwoFactorCode();
            $rider->notify(new RiderTwoFactorCode);
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

    public function logout(Request $request)
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
    public function refreshToken(Request $request)
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
        /** @var Rider $rider */ // Add PHPDoc type hint
        $rider = $request->user('rider-api'); // Authenticated agent
        // return ApiResponse::sendResponseSuccess($agent, 'Agent account deleted successfully');

        try {
            // Delete uploaded files from S3
            $this->riderSerivce->deleteAssets($rider->id);

            // Revoke tokens
            $rider->tokens()->delete();

            // Delete agent from DB
            $rider->delete();
            return ApiResponse::sendResponseSuccess(null, 'Rider account deleted successfully');
        } catch (\Throwable $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
}

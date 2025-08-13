<?php

namespace App\Http\Controllers\AuthTest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRegisterRequest;
use App\Models\Agent;
use App\Models\Rider;
use App\Notifications\Agent\AgentTwoFactorCode;
use App\Notifications\Rider\RiderTwoFactorCode;
use App\Services\AssetsService;
use App\Services\FileServiceFactory;
use App\Services\RiderService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Add this at the top
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RiderAuthTestController extends Controller
{
    protected RiderService $riderSerivce;
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
        $rider = Rider::firstOrCreate(
            ['phone' => $request->phone],
        );
        $token = $rider->createToken('authToken')->plainTextToken;
        $rider->generateTwoFactorCode();
        // $rider->notifyNow(new RiderTwoFactorCode);
        try {
            $rider->notifyNowsdfsdf(new RiderTwoFactorCode);
            Log::info("Notification sent.");
        } catch (\Exception $e) {
            dd($e);
        }

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

            return ApiResponse::sendResponseError('Failed to delete account: ' . $e->getMessage(), 500);
        }
    }
}

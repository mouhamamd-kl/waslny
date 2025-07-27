<?php

namespace App\Http\Controllers\Rider\Auth;

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
    public function login(Request $request)
    {
        try {
            DB::beginTransaction();
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
            // $token = $rider->createToken('authToken')->plainTextToken;
            $rider->generateTwoFactorCode();
            $rider->notify(new RiderTwoFactorCode);
            DB::commit(); // Never reached
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
            DB::beginTransaction();
            $request->user()->currentAccessToken()->delete();
            DB::commit(); // Never reached
            return ApiResponse::sendResponseSuccess(
                [],
                trans_fallback('messages.auth.logout', 'logged out successfully'),
                200
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

    // In AuthController
    public function refreshToken(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->tokens()->delete();
            DB::commit(); // Never reached
            return response()->json([
                'token' => $request->user()->createToken('refresh-token')->plainTextToken,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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

        DB::beginTransaction();
        try {
            // Delete uploaded files from S3
            $this->riderSerivce->deleteAssets($rider->id);

            // Revoke tokens
            $rider->tokens()->delete();

            // Delete agent from DB
            $rider->delete();
            DB::commit();
            return ApiResponse::sendResponseSuccess(null, 'Rider account deleted successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\ApiAdminEmailVerificationRequest;
use App\Models\Admin;
use App\Models\Agent;
use Exception;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    public function verifyEmail(ApiAdminEmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('/api/admin/verified-success'); // or redirect to frontend
    }

    public function resend(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:admins,email'],
            ]);

            $admin = Admin::where('email', $request->email)->first();

            if ($admin->hasVerifiedEmail()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('auth.verification.already_verified', 'This email is already verified.'),
                    400
                );
            }

            $admin->sendEmailVerificationNotification();

            return ApiResponse::sendResponseSuccess(
                null,
                trans_fallback('auth.verification.resent', 'A new verification link has been sent to your email address.'),
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
}

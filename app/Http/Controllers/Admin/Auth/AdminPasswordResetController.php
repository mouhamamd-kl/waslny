<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminResetLinkRequest;
use App\Http\Requests\Admin\Auth\AdminResetPasswordRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class AdminPasswordResetController extends Controller
{
    public function sendResetLink(AdminResetLinkRequest $request)
    {
        try {
            $data = $request->validated();

            $status = Password::broker('admins')->sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? ApiResponse::sendResponseSuccess(
                    null,
                    trans_fallback('messages.password.reset_link_sent', 'Reset link sent to email')
                )
                : ApiResponse::sendResponseError(
                    trans_fallback("messages.password.$status", __($status)),
                    400
                );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred'),
                500,
                get_debug_data($e) // Using the helper function
            );
        }
    }
    public function resetPassword(AdminResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();

            $status = Password::broker('admins')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($admin, $password) {
                    $admin->forceFill([
                        'password' => bcrypt($password),
                    ])->save();
                }
            );
            return $status === Password::PASSWORD_RESET
                ? ApiResponse::sendResponseSuccess(
                    null,
                    trans_fallback('messages.password.reset_success', 'Password reset successfully')
                )
                : ApiResponse::sendResponseError(
                    trans_fallback("messages.password.$status", __($status)),
                    400
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

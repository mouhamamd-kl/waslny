<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRiderIsNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rider = $request->user('rider-api');

        if ($rider->isAccountSuspended()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.account_suspended', 'Your account is suspended.'),
                403 // Forbidden
            );
        }

        return $next($request);
    }
}

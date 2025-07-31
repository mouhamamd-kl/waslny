<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDriverIsNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $driver = $request->user('driver-api');

        if ($driver->isAccountSuspended()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.driver.account_suspended', 'Your account is suspended.'),
                403 // Forbidden
            );
        }

        return $next($request);
    }
}

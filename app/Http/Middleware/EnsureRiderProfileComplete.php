<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRiderProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rider = $request->user('rider-api');

        if (!$rider->isProfileComplete()) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.rider.profile_incomplete', 'rider profile is incomplete'),
                403 // Forbidden
            );
        }

        return $next($request);
    }
}

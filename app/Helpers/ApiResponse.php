<?php

namespace App\Helpers;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Return a successful JSON response
     *
     * @param  mixed  $data  The data to return in the response
     * @param  string  $message  the success message
     * @param  int  $statusCode  the HTTP status Code
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function sendResponseSuccess($data = null, $message = 'Operation successful', $statusCode = 200): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                // 'message' => trans($message),
                'message' => $message,
                'data' => $data,
            ],
            $statusCode
        );
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message  The error message.
     * @param  int  $statusCode  The HTTP status code.
     * @param  mixed  $data  The data to return in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */

    public static function sendResponseError(
        $message = 'Operation failed',
        $statusCode = 400,
        $data = null
    ) {
        return response()->json([
            'status' => 'error',
            'message' => is_string($message) ? trans_fallback($message, $message) : $message,
            'data' => $data,
        ], $statusCode);
    }
    /**
     * Return a paginated JSON response.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator  $paginator  The paginator instance.
     * @param  string  $message  The success message.
     * @param  int  $statusCode  The HTTP status code.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public static function sendResponsePaginated($paginator, $resourceClass, $message = 'Operation successful', $statusCode = 200)
    {
        $paginator->getCollection()->transform(function ($item) use ($resourceClass) {
            return new $resourceClass($item);
        });

        return response()->json(
            [
                'status' => 'success',
                'message' => trans($message),
                'data' => $paginator->items(),
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => $paginator->count(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                    'links' => [
                        'first' => $paginator->url(1),
                        'last' => $paginator->url($paginator->lastPage()),
                        'next' => $paginator->nextPageUrl(),
                        'pervious' => $paginator->previousPageUrl(),
                    ],
                ],
            ],
            $statusCode
        );
    }

    // In ApiResponse helper
    public static function tooManyRequests($message = null, $data = [])
    {
        return self::sendResponseError(
            $message ?? 'Too many requests',
            429,
            $data
        );
    }
}

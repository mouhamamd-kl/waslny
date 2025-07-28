<?php

return [

    /*
     * Structured.
     *
     * If you want folders to be generated based on namespace.
     */

    'structured' => true,

    /*
     * Base URL.
     *
     * The base URL for all of your endpoints.
     */

    'base_url' => env('APP_URL', 'http://localhost'),


    
    /*
     * Auth Middleware.
     *
     * The middleware which wraps your authenticated API routes.
     *
     * E.g. auth:api, auth:sanctum
     *
     * The project uses multiple authentication guards:
     * - 'auth:sanctum' for general users
     * - 'auth:admin-api' for admin routes
     * - 'auth:driver-api' for driver routes
     * - 'auth:rider-api' for rider routes
     *
     * You can change this value to generate Postman collections for different user roles.
     */

    'auth_middleware' => 'auth:sanctum',

];

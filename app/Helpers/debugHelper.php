<?php

if (!function_exists('is_debug_mode')) {
    /**
     * Check if the application is in debug mode
     * 
     * @return bool
     */
    function is_debug_mode(): bool
    {
        return (bool) config('app.debug', false);
    }
}
if (!function_exists('is_console')) {
    /**
     * Determine if the application is running in the console.
     *
     * @return bool
     */
    function is_console(): bool
    {
        return app()->runningInConsole();
    }
}
if (!function_exists('get_debug_data')) {
    /**
     * Get debug information when APP_DEBUG is true
     *
     * @param \Throwable|null $exception
     * @return array
     */
    function get_debug_data(\Throwable $exception): array
    {
        if (!config('app.debug')) {
            return [
                'message' => $exception->getMessage(),
            ];
        }

        $debugData = [
            'debug_mode' => true,
            'environment' => config('app.env'),
        ];

        if ($exception) {
            $debugData['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'type' => get_class($exception),
            ];
        }

        return $debugData;
    }
}

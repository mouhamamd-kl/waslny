<?php
/*
// Vercel Worker: A dedicated, synchronous queue processor.

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// This function is designed to be called by another Vercel function.
// It runs synchronously and processes the queue.

try {
    // 1. Check for a lock to prevent concurrent workers.
    if (Cache::has('queue_worker_running')) {
        Log::info('Queue worker already running. Exiting.');
        http_response_code(200);
        echo json_encode(['message' => 'Queue worker already running.']);
        return;
    }

    // 2. Set a lock that expires automatically as a safety net.
    Cache::put('queue_worker_running', true, now()->addMinutes(4));

    // 3. Run the queue worker synchronously, and release the lock when done.
    try {
        // V1: Using passthru() sent headers prematurely, causing an error.
        // $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work --stop-when-empty --tries=3 --timeout=220';
        // passthru($command);

        // V2: Using exec() without silencing output still caused errors.
        // $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work --stop-when-empty --tries=3 --timeout=220';
        // exec($command, $output, $return_var);
        // Log::info('Queue worker finished. Output: ' . implode("\n", $output));

        // V3 (Correct): Redirect all output to /dev/null to guarantee no premature headers.
        $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work --stop-when-empty --tries=3 --timeout=220 > /dev/null 2>&1';
        exec($command, $output, $return_var);
        Log::info('Queue worker finished with return code: ' . $return_var);
    } finally {
        Cache::forget('queue_worker_running');
    }

    http_response_code(200);
    echo json_encode(['message' => 'Queue processing completed.']);

} catch (Exception $e) {
    Log::error('Error in worker function: ' . $e->getMessage());
    // If an error occurs, ensure the lock is released to prevent a deadlock.
    Cache::forget('queue_worker_running');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
*/

// Vercel Worker: A dedicated, synchronous queue processor.

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

// Correctly bootstrap the Laravel application for console commands.
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// This function is designed to be called by another Vercel function.
// It runs synchronously and processes the queue.

try {
    // 1. Check for a lock to prevent concurrent workers.
    if (Cache::has('queue_worker_running')) {
        Log::info('Queue worker already running. Exiting.');
        http_response_code(200);
        echo json_encode(['message' => 'Queue worker already running.']);
        return;
    }

    // 2. Set a lock that expires automatically as a safety net.
    Cache::put('queue_worker_running', true, now()->addMinutes(4));

    // 3. Run the queue worker synchronously, and release the lock when done.
    try {
        // Redirect all output to /dev/null to guarantee no premature headers.
        $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work --stop-when-empty --tries=3 --timeout=220 > /dev/null 2>&1';
        exec($command, $output, $return_var);
        Log::info('Queue worker finished with return code: ' . $return_var);
    } finally {
        Cache::forget('queue_worker_running');
    }

    http_response_code(200);
    echo json_encode(['message' => 'Queue processing completed.']);

} catch (Exception $e) {
    Log::error('Error in worker function: ' . $e->getMessage());
    // If an error occurs, ensure the lock is released to prevent a deadlock.
    Cache::forget('queue_worker_running');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

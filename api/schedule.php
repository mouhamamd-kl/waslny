<?php
// Vercel Trigger: A non-blocking function to start the queue worker.

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());

try {
    // Authenticate the cron job request.
    if ($request->header('X-Cron-Secret') !== env('CRON_SECRET')) {
        throw new Exception('Unauthorized');
    }

    // Construct the full URL for the worker endpoint.
    $workerUrl = rtrim(env('APP_URL'), '/') . '/api/worker';

    // Use the custom fire-and-forget function to trigger the worker.
    fireAndForgetRequest($workerUrl, [
        'headers' => [
            'X-Cron-Secret' => env('CRON_SECRET'), // Pass the secret for authentication
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode(['message' => 'start worker']), // Body can be minimal
    ]);

    Log::info('Worker trigger successful via fireAndForgetRequest. Dispatched to: ' . $workerUrl);

    http_response_code(200);
    echo json_encode(['message' => 'Queue worker triggered successfully.']);

} catch (Exception $e) {
    Log::error('Error in trigger function: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$response->send();
$kernel->terminate($request, $response);

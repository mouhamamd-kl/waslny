<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Events\TestNotification;
use Illuminate\Http\Request;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());

// Handle request directly without Laravel router
try {
    if ($request->method() !== 'POST') {
        throw new Exception('Invalid method');
    }

    // Verify secret
    if ($request->header('X-Driver-Search-Secret') !== env('DRIVER_SEARCH_SECRET')) {
        throw new Exception('Unauthorized');
    }

    // Fire event

    for ($i = 0; $i < 200; $i++) {
        event(new TestNotification([
            'message' => 'this is it bitches'
        ]));
        sleep(60);
    }
    http_response_code(200);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTrace() // Optional for debugging
    ]);
}

$response->send();
$kernel->terminate($request, $response);

<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());

// Handle request directly without Laravel router
try {
    if ($request->header('X-Cron-Secret') !== env('CRON_SECRET')) {
        throw new Exception('Unauthorized');
    }

    $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work';
    passthru($command);    // Run multiple queue workers in the background
    // for ($i = 0; $i < 3; $i++) {
    //     $command = 'php ' . realpath(__DIR__ . '/../artisan') . ' queue:work --stop-when-empty --tries=1';
    //     $output = [];
    //     $return_var = 0;
    //     exec($command, $output, $return_var);
    //     Log::info('Queue worker output: ' . implode("\n", $output));
    // }

    http_response_code(200);
    echo json_encode(['message' => 'Queue processing started']);
} catch (Exception $e) {
    Log::info($e);
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}

$response->send();
$kernel->terminate($request, $response);

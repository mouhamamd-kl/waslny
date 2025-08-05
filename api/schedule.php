<?php

// Secure the endpoint

use App\Events\TestNotification;

if (!isset($_SERVER['HTTP_X_CRON_SECRET']) || $_SERVER['HTTP_X_CRON_SECRET'] !== getenv('CRON_SECRET')) {
    http_response_code(401);
    die('Unauthorized');
}

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';

// Create a new kernel instance
$kernel = app()->make(Illuminate\Contracts\Console\Kernel::class);

event(new TestNotification([
    'قائد الطوفان' => 'القائد يحيى السنوار'
]));

// Call the schedule:run command
$kernel->call('schedule:run');

// Manually call the scheduled trip commands
$kernel->call('trips:process-scheduled');
$kernel->call('trips:activate-scheduled');

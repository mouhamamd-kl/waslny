<?php

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';

// Create a new kernel instance
$kernel = app()->make(Illuminate\Contracts\Console\Kernel::class);

// Call the schedule:run command
$kernel->call('schedule:run');

// Manually call the scheduled trip commands
$kernel->call('trips:process-scheduled');
$kernel->call('trips:activate-scheduled');

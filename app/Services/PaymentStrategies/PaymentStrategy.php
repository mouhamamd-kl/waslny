<?php

namespace App\Services\PaymentStrategies;

use App\Models\Driver;
use App\Models\Rider;

interface PaymentStrategy
{
    public function pay(Rider $rider, Driver $driver, int $fare, int $commission): bool;
}

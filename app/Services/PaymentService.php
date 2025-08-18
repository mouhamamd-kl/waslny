<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Rider;
use App\Models\Driver;

class PaymentService
{
    public function processPayment(Trip $trip, int $fare, int $commission): bool
    {
        try {
            $strategy = PaymentStrategyFactory::make($trip->paymentMethodAsEnum());
            return $strategy->pay($trip->rider, $trip->driver, $fare, $commission);
        } catch (\InvalidArgumentException $e) {
            report($e);
            return false;
        }
    }
}

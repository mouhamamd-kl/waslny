<?php

namespace App\Services\PaymentStrategies;

use App\Models\Driver;
use App\Models\Rider;
use App\Models\SystemWallet;
use Exception;
use Illuminate\Support\Facades\DB;

class CashPaymentStrategy implements PaymentStrategy
{
    public function pay(Rider $rider, Driver $driver, int $fare, int $commission): bool
    {
        if ($commission <= 0) {
            return true; // No commission to collect.
        }

        $systemWallet = SystemWallet::first();
        if (!$systemWallet) {
            // Or handle this case as per your application's requirements
            throw new Exception('System wallet not found.');
        }

        try {
            // The driver's wallet pays the commission to the system.
            // The bavix-wallet package allows for negative balances,
            // accurately reflecting any debt the driver owes.
            $driver->transfer($systemWallet, $commission, ['description' => 'Commission for cash trip']);

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}

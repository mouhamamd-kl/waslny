<?php

namespace App\Services\PaymentStrategies;

use App\Models\Driver;
use App\Models\Rider;
use App\Models\SystemWallet;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletPaymentStrategy implements PaymentStrategy
{
    public function pay(Rider $rider, Driver $driver, int $fare, int $commission): bool
    {
        if (!$rider->canWithdraw($fare)) {
            return false;
        }

        $systemWallet = SystemWallet::first();
        if (!$systemWallet) {
            // Or handle this case as per your application's requirements
            throw new Exception('System wallet not found.');
        }

        try {
            DB::transaction(function () use ($rider, $driver, $systemWallet, $fare, $commission) {
                // Step 1: Rider pays the full fare to the system.
                $rider->transfer($systemWallet, $fare, ['description' => 'Trip fare payment']);

                // Step 2: System pays the driver their earnings (fare - commission).
                $driverEarnings = $fare - $commission;
                if ($driverEarnings > 0) {
                    $systemWallet->transfer($driver, $driverEarnings, ['description' => 'Driver earnings for trip']);
                }
            });

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}

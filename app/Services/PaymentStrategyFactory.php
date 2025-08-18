<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Services\PaymentStrategies\CashPaymentStrategy;
use App\Services\PaymentStrategies\PaymentStrategy;
use App\Services\PaymentStrategies\WalletPaymentStrategy;
use InvalidArgumentException;

class PaymentStrategyFactory
{
    public static function make(PaymentMethodEnum $paymentMethod): PaymentStrategy
    {
        return match ($paymentMethod) {
            PaymentMethodEnum::WALLET => new WalletPaymentStrategy(),
            PaymentMethodEnum::CASH => new CashPaymentStrategy(),
            default => throw new InvalidArgumentException("Unsupported payment method: {$paymentMethod->value}"),
        };
    }
}

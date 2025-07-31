<?php

namespace App\Services;

use Bavix\Wallet\Interfaces\Wallet;

class SystemWalletService
{
    public function __construct(protected Wallet $wallet)
    {
    }

    public function getBalance(): float
    {
        return $this->wallet->balanceFloat;
    }

    public function deposit(float $amount, array $meta = [], bool $confirmed = true): void
    {
        $this->wallet->deposit($amount, $meta, $confirmed);
    }

    public function withdraw(float $amount, array $meta = [], bool $confirmed = true): void
    {
        $this->wallet->withdraw($amount, $meta, $confirmed);
    }

    public function transfer(Wallet $wallet, float $amount, array $meta = []): void
    {
        $this->wallet->transfer($wallet, $amount, $meta);
    }
}

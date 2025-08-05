<?php

namespace App\Services;

use App\Models\SystemWallet;
use App\Helpers\CacheHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service class for managing the SystemWallet.
 *
 * This class provides a centralized way to interact with the system's wallet,
 * handling all business logic related to wallet operations such as retrieving
 * the balance and processing transactions.
 */
class SystemWalletService extends BaseService
{
    /**
     * SystemWalletService constructor.
     *
     * @param CacheHelper $cache The cache helper instance.
     */
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new SystemWallet, $cache);
    }

    /**
     * Retrieve the current balance of the system wallet.
     *
     * This method fetches the first wallet record and returns its balance.
     * If no wallet exists, it returns 0.0.
     *
     * @return float The current balance.
     */
    public function getBalance(): float
    {
        $wallet = $this->model->first();
        return $wallet ? (float) $wallet->balance : 0.0;
    }

    /**
     * Add funds to the system wallet.
     *
     * @param float $amount The amount to add. Must be positive.
     * @return SystemWallet The updated wallet instance.
     * @throws InvalidArgumentException If the amount is not positive.
     */
    public function credit(float $amount): SystemWallet
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($amount) {
            $wallet = $this->model->lockForUpdate()->firstOrCreate([], ['balance' => 0]);
            $wallet->balance += $amount;
            $wallet->save();
            $this->invalidateCache();
            return $wallet;
        });
    }

    /**
     * Remove funds from the system wallet.
     *
     * @param float $amount The amount to remove. Must be positive.
     * @return SystemWallet The updated wallet instance.
     * @throws InvalidArgumentException If the amount is not positive or if insufficient funds.
     */
    public function debit(float $amount): SystemWallet
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($amount) {
            $wallet = $this->model->lockForUpdate()->firstOrCreate([], ['balance' => 0]);

            if ($wallet->balance < $amount) {
                throw new InvalidArgumentException('Insufficient funds.');
            }

            $wallet->balance -= $amount;
            $wallet->save();
            $this->invalidateCache();
            return $wallet;
        });
    }
}

<?php

namespace App\Providers;

use App\Models\SystemWallet;
use App\Services\SystemWalletService;
use Bavix\Wallet\Interfaces\Wallet;
use Illuminate\Support\ServiceProvider;

class SystemWalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SystemWalletService::class, function ($app) {
            $systemWallet = SystemWallet::firstOrCreate(['name' => 'System Wallet']);
            return new SystemWalletService($systemWallet);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

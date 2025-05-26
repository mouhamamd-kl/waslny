<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFromMultiplePaths();
    }

    protected function loadMigrationsFromMultiplePaths()
    {
        $domains = [
            'users',
            'vehicles',
            'trips',
            'payments',
            'geography',
            'coupons'
        ];

        foreach ($domains as $domain) {
            $path = database_path("/domains/{$domain}/migrations");
            if (File::exists($path)) {
                $this->loadMigrationsFrom($path);
            }
        }
    }
}

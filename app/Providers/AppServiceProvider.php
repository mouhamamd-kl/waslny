<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $mainPath = database_path('migrations');
        $directories = glob($mainPath.'/domains/*/*', GLOB_ONLYDIR);
        $directories2 = glob($mainPath.'/domains/*/*/*', GLOB_ONLYDIR);

        
        $this->loadMigrationsFrom(array_merge([$mainPath], $directories,$directories2));
    }
}

<?php

namespace App\Providers;

use App\Events\TripCreated;
use App\Listeners\StartDriverSearch;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Event::listen(
        //     TripCreated::class,
        //     StartDriverSearch::class,
        // );

        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/domains/*/*', GLOB_ONLYDIR);
        $directories2 = glob($mainPath . '/domains/*/*/*', GLOB_ONLYDIR);
        $this->loadMigrationsFrom(array_merge([$mainPath], $directories, $directories2));

        // Broadcast::channel('trip.{tripId}', function ($user, $tripId) {
        //     return [
        //         'user_id' => $user->id,
        //         'connected_at' => now(),
        //         'device_id' => request()->header('X-Device-ID')
        //     ];
        // });

        // // Track connections/disconnections
        // Broadcast::connection(function ($connection) {
        //     ConnectionLog::create([
        //         'user_id' => $connection->user->id,
        //         'connected_at' => now(),
        //         'connection_id' => $connection->socketId
        //     ]);
        // });

        // Broadcast::disconnection(function ($connection) {
        //     ConnectionLog::where('connection_id', $connection->socketId)
        //         ->update(['disconnected_at' => now()]);
        // });
    }
}

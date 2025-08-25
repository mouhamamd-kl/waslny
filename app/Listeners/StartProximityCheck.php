<?php

namespace App\Listeners;

use App\Events\DriverEnRouteToPickup;
use App\Jobs\CheckDriverProximity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StartProximityCheck implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\DriverEnRouteToPickup  $event
     * @return void
     */
    public function handle(DriverEnRouteToPickup $event): void
    {
        CheckDriverProximity::dispatch($event->trip);
    }
}

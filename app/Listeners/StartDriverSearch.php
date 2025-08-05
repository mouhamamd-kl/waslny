<?php

namespace App\Listeners;

use App\Events\TripCreated;
use App\Jobs\FindDriverForTrip;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StartDriverSearch implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\TripCreated  $event
     * @return void
     */
    public function handle(TripCreated $event)
    {
        // trip_flow
        FindDriverForTrip::dispatch($event->trip);
    }
}

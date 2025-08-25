<?php

namespace App\Listeners;

use App\Events\TestNotification;
use App\Events\TripCreated;
use App\Jobs\FindDriverForTrip;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StartDriverSearch implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'default';

    /**
     * Handle the event.
     *
     * @param  \App\Events\TripCreated  $event
     * @return void
     */
    public function handle(TripCreated $event)
    {
        //TODO
        Log::info(['StartDriverSearch listener handled for trip ID: ' . $event->trip->id]);
        event(new TestNotification(['StartDriverSearch handled for trip ' . $event->trip->id]));
        // trip_flow
        if ($event->trip != null) {
            FindDriverForTrip::dispatch($event->trip);
        }
    }
}

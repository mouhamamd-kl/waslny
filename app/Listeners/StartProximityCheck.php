<?php

namespace App\Listeners;

use App\Events\DriverAssigned;
use App\Jobs\CheckDriverProximity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

// class StartProximityCheck implements ShouldQueue
// {
//     use InteractsWithQueue;

//     /**
//      * Handle the event.
//      *
//      * @param  \App\Events\DriverAssigned  $event
//      * @return void
//      */
//     public function handle(DriverAssigned $event)
//     {
//         // trip_flow
//         CheckDriverProximity::dispatch($event->trip);
//     }
// }

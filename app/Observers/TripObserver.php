<?php

namespace App\Observers;

use App\Models\Trip;

class TripObserver
{
    /**
     * Handle the Trip "created" event.
     */
    public function created(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "updated" event.
     */
    public function updated(Trip $trip)
    {
        if ($trip->isDirty('status')) {
            // event(new TripStatusChanged(
            //     $trip,
            //     $trip->getOriginal('status'),
            //     $trip->status
            // ));
        }
    }

    /**
     * Handle the Trip "deleted" event.
     */
    public function deleted(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "restored" event.
     */
    public function restored(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "force deleted" event.
     */
    public function forceDeleted(Trip $trip): void
    {
        //
    }
}

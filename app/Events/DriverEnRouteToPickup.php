<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverEnRouteToPickup
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Trip $trip;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Trip $trip
     * @return void
     */
    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }
}

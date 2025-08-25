<?php

namespace App\Events;

use App\Http\Resources\DriverResource;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverPreAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Trip $trip;
    public Driver $driver;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Trip $trip, Driver $driver)
    {
        $this->trip = $trip;
        $this->driver = $driver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('riders.' . $this->trip->rider_id),
            new PrivateChannel('drivers.' . $this->driver->id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'driver.pre_assigned';
    }

    public function broadcastWith()
    {
        return [
            'trip' => (new TripResource($this->trip->fresh()))->resolve(),
            'driver' => (new DriverResource($this->driver->fresh()))->resolve(),
        ];
    }
}

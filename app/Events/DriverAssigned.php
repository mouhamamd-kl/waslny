<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Http\Resources\DriverResource;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Trip;

class DriverAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $driver;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Trip $trip
     * @param \App\Models\Driver $driver
     */
    public function __construct(Trip $trip, Driver $driver)
    {
        $this->trip = $trip;
        $this->driver = $driver;
    }

    /**
     * The channel the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('riders.' . $this->trip->rider_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'driver.assigned';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'trip' => new TripResource($this->trip),
            'driver' => new DriverResource($this->driver),
            'sent_at' => now()->toISOString()
        ];
    }
}

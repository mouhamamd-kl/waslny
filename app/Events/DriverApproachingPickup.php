<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\DriverResource;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverApproachingPickup implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $driver;

    /**
     * Create a new event instance.
     *
     * @param array $tripData
     * @param int $driverId
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
        // return new PrivateChannel('driver.' . $this->driverId);

        return new PrivateChannel(
            BroadCastChannelEnum::TRIP->bind(
                'trip', $this->trip->id
            )
        );
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'driver.approaching.pickup';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'trip' => (new TripResource($this->trip))->resolve(),
            'driver' => (new DriverResource($this->driver))->resolve(),
            'sent_at' => now()->toISOString()
        ];
    }
}

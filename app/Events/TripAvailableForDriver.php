<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripAvailableForDriver implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $driverId;

    /**
     * Create a new event instance.
     *
     * @param array $tripData
     * @param int $driverId
     */
    public function __construct(Trip $trip, int $driverId)
    {
        $this->trip = $trip;
        $this->driverId = $driverId;
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
            BroadCastChannelEnum::DRIVER->bind(
                'driver', $this->driverId
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
        return 'trip.available';
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
            'sent_at' => now()->toISOString()
        ];
    }
}

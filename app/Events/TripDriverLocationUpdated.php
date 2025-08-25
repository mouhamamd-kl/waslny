<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Enums\channels\BroadCastChannelEnum;

class TripDriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $tripId;
    public $location;

    /**
     * Create a new event instance.
     */
    public function __construct($tripId, $location)
    {
        $this->tripId = $tripId;
        $this->location = $location;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(BroadCastChannelEnum::TRIP->bind($this->tripId)),
        ];
    }

    public function broadcastAs()
    {
        return 'trip.driver.location.updated';
    }

    public function broadcastWith()
    {
        return [
            'location' => $this->location,
        ];
    }
}

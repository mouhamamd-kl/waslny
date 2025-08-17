<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\TripResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripLocationCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    public $tripLocation;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Models\Trip $trip, \App\Models\TripLocation $tripLocation)
    {
        $this->trip = $trip;
        $this->tripLocation = $tripLocation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(BroadCastChannelEnum::TRIP->bind($this->trip->id)),
        ];
    }

    public function broadcastAs()
    {
        return 'trip.location.completed';
    }

    public function broadcastWith()
    {
        return [
            'trip' => (new TripResource($this->trip))->resolve(),
        ];
    }
}

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

class TripCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $trip;
    public $tripChannel;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
        $this->tripChannel = BroadCastChannelEnum::TRIP->bind(
            'trip', $trip->id
        );
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            BroadCastChannelEnum::TRIP->bind(
                'trip', $this->trip->id
            )
        );
    }

    public function broadcastAs()
    {
        return 'trip.completed';
    }

    public function broadcastWith()
    {
        return [
            'trip' => (new TripResource($this->trip))->resolve(),
        ];
    }
}

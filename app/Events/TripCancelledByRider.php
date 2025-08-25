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

class TripCancelledByRider implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public Trip $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function broadcastOn()
    {
        if (!$this->trip->driver_id) {
            return [];
        }
        return new PrivateChannel(
            BroadCastChannelEnum::DRIVER->bind(
                $this->trip->driver_id
            )
        );
    }

    public function broadcastAs()
    {
        return 'trip.cancelled.by.rider';
    }

    public function broadcastWith()
    {
        return [
            'trip' => (new TripResource($this->trip->fresh()))->resolve()
        ];
    }
}

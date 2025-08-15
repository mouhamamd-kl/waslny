<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripTimeNear implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function broadcastOn()
    {
        return [
            //to make private again
            // new PrivateChannel(BroadCastChannelEnum::DRIVER->bind(
            //     $this->trip->driver_id,
            // )),
            // new PrivateChannel(BroadCastChannelEnum::RIDER->bind(
            //     $this->trip->rider_id,
            // )),

            new Channel(BroadCastChannelEnum::DRIVER->bind(
                $this->trip->driver_id,
            )),
            new Channel(BroadCastChannelEnum::RIDER->bind(
                $this->trip->rider_id,
            )),
        ];
    }

    public function broadcastAs()
    {
        return 'trip.near';
    }

    public function broadcastWith()
    {
        return [
            'trip' => (new TripResource($this->trip))->resolve(),
        ];
    }
}

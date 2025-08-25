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

class TripCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Trip $trip;
    public string $tripChannel;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
        $this->tripChannel = BroadCastChannelEnum::TRIP->bind(
            $trip->id
        );
    }

    public function broadcastOn()
    {
        // to make private again
        return new PrivateChannel(
            BroadCastChannelEnum::RIDER->bind(
                $this->trip->rider_id
            )
        );

        // return new Channel(
        //     BroadCastChannelEnum::RIDER->bind(
        //         $this->trip->rider_id
        //     )
        // );
    }

    public function broadcastAs()
    {
        return 'trip.created';
    }

    public function broadcastWith()
    {
        return [
            'channel' => $this->tripChannel,
            'trip' => (new TripResource($this->trip))->resolve(),
        ];
    }
}

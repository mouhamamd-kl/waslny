<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripCancelledByDriver implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $trip;
    public $riderId;

    public function __construct(int $riderId)
    {
        $this->riderId = $riderId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            BroadCastChannelEnum::RIDER->bind([
                $this->riderId
            ])
        );
    }

    public function broadcastAs()
    {
        return 'trip.cancelled';
    }

    public function broadcastWith()
    {
        return [];
    }
}

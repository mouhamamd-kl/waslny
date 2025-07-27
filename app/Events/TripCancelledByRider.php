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

class TripCancelledByRider implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $trip;
    public $driverId;

    public function __construct(int $driverId)
    {
        $this->driverId = $driverId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            BroadCastChannelEnum::DRIVER->bind([
                $this->driverId
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

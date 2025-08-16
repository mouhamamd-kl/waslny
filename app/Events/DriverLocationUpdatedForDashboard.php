<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Enums\channels\BroadCastChannelEnum;

class DriverLocationUpdatedForDashboard implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driverId;
    public $location;

    /**
     * Create a new event instance.
     */
    public function __construct($driverId, $location)
    {
        $this->driverId = $driverId;
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
            new PresenceChannel(BroadCastChannelEnum::DRIVERS_ONLINE->value),
        ];
    }

    public function broadcastAs()
    {
        return 'driver.location.updated';
    }

    public function broadcastWith()
    {
        return [
            'driver_id' => $this->driverId,
            'location' => $this->location,
        ];
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tripData;
    public $driverData;
    public $riderId;
    /**
     * Create a new event instance.
     *
     * @param array $tripData
     * @param int $driverId
     */
    public function __construct(array $tripData, array $driverData, int $riderId)
    {
        $this->tripData = $tripData;
        $this->driverData = $driverData;
        $this->riderId = $riderId;
    }

    /**
     * The channel the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('rider.' . $this->riderId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'driver.assigned';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'trip' => $this->tripData,
            'driver' => $this->driverData,
            'sent_at' => now()->toISOString()
        ];
    }
}

<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\DriverResource;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverArrived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    protected Driver $driver;
    protected Trip $trip;
    public function __construct(Driver $driver, Trip $trip)
    {
        $this->driver = $driver;
        $this->trip = $trip;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [
            //to make private again
            // new PrivateChannel(
            //     BroadCastChannelEnum::TRIP->bind(
            //         $this->trip->id
            //     )
            // ),

            new Channel(
                BroadCastChannelEnum::TRIP->bind(
                    $this->trip->id
                )
            ),
        ];
    }

    public function broadcastAs()
    {
        return 'driver.arrived';
    }

    public function broadcastWith()
    {
        return [
            'trip' => new TripResource($this->trip),
            'driver' => new DriverResource($this->driver),
        ];
    }
}

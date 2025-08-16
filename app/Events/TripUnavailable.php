<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;

class TripUnavailable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Trip $trip;
    public Collection $drivers;

    /**
     * Create a new event instance.
     */
    public function __construct(Trip $trip, Collection $drivers)
    {
        $this->trip = $trip;
        $this->drivers = $drivers;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return array_map(function ($driver) {
            return BroadCastChannelEnum::DRIVER->bind($driver->id);
        }, $this->drivers->all());
    }

    public function broadcastAs()
    {
        return 'trip.unavailable';
    }

    public function broadcastWith()
    {
        return ['trip_id' => $this->trip->id];
    }
}

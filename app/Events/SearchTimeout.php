<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchTimeout implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The trip instance.
     *
     * @var Trip
     */
    public Trip $trip;

    /**
     * Create a new event instance.
     *
     * @param Trip $trip
     */
    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->getChannelName()),
        ];
    }

    /**
     * Get the broadcast channel name for the event.
     *
     * @return string
     */
    public function getChannelName(): string
    {
        return BroadCastChannelEnum::TRIP->bind(['tripId' => $this->trip->id]);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'search.timeout';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'status' => 'timeout',
            'message' => $this->getTimeoutMessage(),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get timeout message.
     *
     * @return string
     */
    protected function getTimeoutMessage(): string
    {
        return trans_fallback(
            key: 'messages.trip.no_drivers_available',
            default: 'No drivers available. Please try again later.'
        );
    }
}

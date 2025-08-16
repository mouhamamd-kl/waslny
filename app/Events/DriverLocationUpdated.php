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

use App\Enums\channels\BroadCastChannelEnum;

// class DriverLocationUpdated implements ShouldBroadcastNow
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $tripId;
//     public $driverId;
//     public $location;

//     /**
//      * Create a new event instance.
//      */
//     public function __construct($tripId, $driverId, $location)
//     {
//         $this->tripId = $tripId;
//         $this->driverId = $driverId;
//         $this->location = $location;
//     }

//     /**
//      * Get the channels the event should broadcast on.
//      *
//      * @return array<int, \Illuminate\Broadcasting\Channel>
//      */
//     public function broadcastOn(): array
//     {
//         return [
//             new PrivateChannel(BroadCastChannelEnum::TRIP->bind($this->tripId)),
//             new PresenceChannel(BroadCastChannelEnum::DRIVERS_ONLINE->value),
//         ];
//     }

//     public function broadcastAs()
//     {
//         return 'driver.location.updated';
//     }

//     public function broadcastWith()
//     {
//         return [
//             'driver_id' => $this->driverId,
//             'location' => $this->location,
//         ];
//     }
// }

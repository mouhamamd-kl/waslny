<?php

namespace App\Events;

use App\Enums\channels\BroadCastChannelEnum;
use App\Http\Resources\AccountSuspensionResource;
use App\Models\AccountSuspension;
use App\Models\Driver;
use App\Models\Rider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountSuspended implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $user;

    public function __construct(
        Model $user,
        public AccountSuspension $suspension
    ) {
        $this->user = $user;
    }

    public function broadcastOn(): PrivateChannel
    {
        $channel = $this->user instanceof Driver
            ? BroadCastChannelEnum::DRIVER
            : BroadCastChannelEnum::RIDER;

        $key = $this->user instanceof Driver ? 'driverId' : 'riderId';

        return new PrivateChannel(
            $channel->bind([
                $key => $this->user->id
            ])
        );
    }

    public function broadcastAs(): string
    {
        return 'account.suspended';
    }

    public function broadcastWith(): array
    {
        return [
            'suspension' => (new AccountSuspensionResource($this->suspension))->resolve(),
        ];
    }
}

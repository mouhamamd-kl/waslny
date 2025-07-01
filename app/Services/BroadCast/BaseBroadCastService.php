<?php

namespace App\Services\BroadCast;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Event;

class BaseBroadCastService
{
    /**
     * Broadcast data to channels immediately or queued
     *
     * @param string|array $channels
     * @param string $eventName
     * @param array $payload
     * @param bool $queue = true
     */
    public function broadcast($channels, Event $event, array $payload, bool $queue = true): void
    {
        try {
            $channels = is_array($channels) ? $channels : [$channels];

            if ($queue) {
                event($event); // Dispatches to queue if configured
            } else {
                broadcast($event); // Sends immediately
            }
        } catch (Exception $e) {
            Log::error("Broadcast failed: " . $e->getMessage(), [
                'channels' => $channels,
                'event' => $event::class,
                'payload' => $payload
            ]);
        }
    }

    /**
     * Broadcast to a public channel
     */
    public function toPublic(string $channel, Event $event, array $data, bool $queue = true): void
    {
        $this->broadcast(new Channel($channel), $event, $data, $queue);
    }

    /**
     * Broadcast to a private channel
     */
    public function toPrivate(string $channel, Event $event, array $data, bool $queue = true): void
    {
        $this->broadcast(new PrivateChannel($channel), $event, $data, $queue);
    }

    /**
     * Broadcast to a presence channel
     */
    public function toPresence(string $channel, Event $event, array $data, bool $queue = true): void
    {
        $this->broadcast(new PresenceChannel($channel), $event, $data, $queue);
    }

    /**
     * Broadcast to a user's private channel
     */
    public function toUser(int $userId, Event $event, array $data, bool $queue = true): void
    {
        $this->toPrivate('user.' . $userId, $event, $data, $queue);
    }
}

<?php

namespace App\Services\Notification;

use App\Contracts\Notifiable;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationBase;

class NotificationService
{
    protected bool $loggingEnabled = true;

    protected bool $queueEnabled = false;

    protected int $chunkSize = 200;

    public function __construct() {}

    /**
     * Send a notification to multiple notifiables
     */
    public function send(
        Collection $notifiables,
        Notification $notification,
    ): void {
        $this->queueEnabled ?
            $notifiables->chunk($this->chunkSize, function ($chunk) use ($notification) {
                $this->sendToChunk($chunk, $notification);
            }) :
            $this->sendToChunk($notifiables, $notification);
    }

    /**
     * Send notification to a single notifiable
     */
    public function sendToUser(
        User $user,
        Notification $notification,
        array $channels
    ): void {
        $this->sendToChunk(collect([$user]), $notification);
    }

    protected function sendToChunk(
        Collection $notifiables,
        Notification $notification,
    ): void {
        try {
            if ($this->shouldSend($notifiables, $notification)) {
                $this->queueEnabled
                    ? NotificationBase::send($notifiables, $notification)
                    : NotificationBase::sendNow($notifiables, $notification);
            }
        } catch (\Exception $e) {
            $this->handleError($e, $notifiables, $notification);
        }
    }

    /**
     * Check if notification should be sent
     */
    protected function shouldSend(
        Collection $notifiables,
        Notification $notification
    ): bool {
        return $notifiables->isNotEmpty();
    }

    /**
     * Handle notification errors
     */
    protected function handleError(
        \Exception $e,
        Collection $notifiables,
        Notification $notification
    ): void {
        Log::error("Notification failed: {$e->getMessage()}", [
            'notification' => get_class($notification),
            'notifiables' => $notifiables->pluck('id'),
            'exception' => $e,
        ]);
    }

    /**
     * Toggle notification logging
     */
    public function toggleLogging(bool $status): self
    {
        $this->loggingEnabled = $status;

        return $this;
    }

    /**
     * Toggle queuing of notifications
     */
    public function toggleQueue(bool $status): self
    {
        $this->queueEnabled = $status;

        return $this;
    }

    /**
     * Set notification chunk size
     */
    public function setChunkSize(int $size): self
    {
        $this->chunkSize = $size;

        return $this;
    }
}

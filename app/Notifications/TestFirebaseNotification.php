<?php

namespace App\Notifications;

use App\Channels\FirebaseChannel;
use App\Channels\Messages\FirebaseMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestFirebaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }

    public function toFirebase($notifiable)
    {
        return (new FirebaseMessage)
            ->title('Hello from Firebase')
            ->body('This is a test notification.')
            ->data(['key' => 'value']);
    }
}

<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FirebaseChannel
{
    protected $cloud_message;
    public function __construct(CloudMessage $cloud_message)
    {
        $this->cloud_message = $cloud_message;
    }


    public function send($notifiable, Notification $notification)
    {

        $message = $notification->toFirebase($notifiable);
        $token = $notifiable->routeNotificationFor('firebase', $notification);

        if (!$token) {
            return;
        }

        $messaging = app('firebase.messaging');

        $cloudMessage = $this->cloud_message->toToken($token)
            ->withNotification(FirebaseNotification::create($message->title, $message->body))
            ->withData($message->data);

        $messaging->send($cloudMessage);
    }
}

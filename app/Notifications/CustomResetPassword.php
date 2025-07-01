<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = config('app.frontend_url').'/reset-password?'
            .http_build_query([
                'token' => $this->token,
                'email' => $notifiable->email,
            ]);
        $expireTime = config('auth.passwords.users.expire');

        return (new MailMessage)
            ->subject(trans_fallback(
                'messages.password.reset_subject',
                'Password Reset Request'
            ))
            ->view('auth.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'expireTime' => $expireTime,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

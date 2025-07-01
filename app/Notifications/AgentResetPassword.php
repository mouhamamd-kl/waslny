<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = config('app.frontend_url').'/agent/reset-password?'
            .http_build_query([
                'token' => $this->token,
                'email' => $notifiable->company_email,
            ]);

        return (new MailMessage)
            ->subject(trans_fallback(
                'messages.password.reset_subject',
                'Agent Password Reset Request'
            ))
            ->view('auth.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'expireTime' => config('auth.passwords.agents.expire'),
            ]);
    }
}

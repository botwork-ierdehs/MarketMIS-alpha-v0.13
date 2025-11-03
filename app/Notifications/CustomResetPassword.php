<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    // ✅ This method tells Laravel to send the notification via email
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->from('onotreply@gmail.com', 'MARKETMIS') // 👈 Name only
            ->subject('Reset Your Password')
            ->markdown('emails.reset-password', [
                'url' => $url,
                'email' => $notifiable->getEmailForPasswordReset(),
                'expire' => Carbon::now()->addMinutes(60),
            ]);
    }
}
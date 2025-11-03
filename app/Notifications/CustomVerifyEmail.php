<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        $verifyUrl = $this->customVerifyUrl($notifiable);

        return (new MailMessage)
            ->from('onotreply@gmail.com', 'MARKETMIS') // 👈 Name only
            ->subject('Please Verify Your Email Address')
            ->markdown('emails.custom-verify', [
                'url' => $verifyUrl,
                'user' => $notifiable,
                'expire' => Carbon::now()->addMinutes(60),
            ]);
    }

    protected function customVerifyUrl($notifiable)
    {
        Log::info('Login attempt', [
            'notifiable' => $notifiable->getKey() 
        ]);
     return URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(60),
        [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]
        );
    }
}


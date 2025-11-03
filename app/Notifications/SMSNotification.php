<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class SMSNotification extends Notification
{
    use Queueable;
    
    protected $message;
    /**
     * Create a new notification instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['twilio'];
    }

    public function toTwilio($notifiable)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        $twilio = new Client($sid, $token);

        $twilio->messages->create(
            $notifiable->routeNotificationForTwilio(), // recipient number
            [
                'from' => $from,
                'body' => $this->message,
            ]
        );
    }
}

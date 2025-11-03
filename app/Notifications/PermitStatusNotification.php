<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermitStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database']; // 👈 store in DB for in-app
    }

    public function toDatabase($notifiable)
    {
        return [
            'permit_id' => $this->permit->id,
            'status'    => $this->status,
            'message'   => "Your permit has been {$this->status}.",
            'url'       => url("/permits/{$this->permit->id}"),
        ];
    }

}

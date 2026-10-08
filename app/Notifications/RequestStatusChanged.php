<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusChanged extends Notification
{
    use Queueable;
    protected $request;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Request  $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Ticket Status Updated',
            'message' => "The status of your ticket '{$this->request->title}' has been updated to '{$this->request->status}'.",
            'request_id' => $this->request->id,
            'updated_by' => auth()->user()->name,
        ];
    }
}

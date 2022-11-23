<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Conference;

class AdminDeletedConferenceMail extends Notification
{
    use Queueable;

    protected $conferenceTitle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($conferenceTitle)
    {
        $this->conferenceTitle = $conferenceTitle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $conferenceTitle = $this->conferenceTitle;

        return (new MailMessage)->markdown('mail.admin_deleted_conference', compact('conferenceTitle'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

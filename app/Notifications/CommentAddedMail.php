<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\User;
use App\Models\Lecture;

class CommentAddedMail extends Notification
{
    use Queueable;

    protected $user;
    protected $conference;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Lecture $lecture)
    {
        $this->user = $user;
        $this->lecture = $lecture;
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
        $user = $this->user;
        $lecture = $this->lecture;

        return (new MailMessage)->markdown('mail.comment_added', compact('user', 'lecture'));
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

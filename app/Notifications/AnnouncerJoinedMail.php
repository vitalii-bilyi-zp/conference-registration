<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

use App\Models\Lecture;

class AnnouncerJoinedMail extends Notification
{
    use Queueable;

    protected $lecture;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Lecture $lecture)
    {
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
        $user = $this->lecture->user;
        $conference = $this->lecture->conference;
        $frontendUrl = config('frontend.url');
        $conferenceUrl = config('frontend.conferences_url') . '/' . $conference->id;
        $lectureUrl = config('frontend.lectures_url') . '/' . $this->lecture->id;
        $conferenceLink = '<a href="' . $frontendUrl . $conferenceUrl . '">' . $conference->title . '</a>';
        $lectureLink = '<a href="' . $frontendUrl . $lectureUrl . '">' . $this->lecture->title . '</a>';
        $htmlMessage = 'Добрый день, на конференцию ' . $conferenceLink . ' присоединился  новый участник ' . $user->firstname . ' ' . $user->lastname . ' с докладом на тему ' . $lectureLink;

        return (new MailMessage)->line(new HtmlString($htmlMessage));
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

<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Lecture;
use App\Models\User;

use App\Notifications\AnnouncerJoinedMail;

class SendAnnouncerJoinedEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lecture;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lecture $lecture)
    {
        $this->lecture = $lecture;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $conference = $this->lecture->conference;
        $conferenceListeners = $conference->users()
            ->where('type', '=', User::LISTENER_TYPE)
            ->get();

        $conferenceListeners->each(function ($user) {
            $user->notify(new AnnouncerJoinedMail($this->lecture));
        });
    }
}

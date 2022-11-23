<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Conference;

use App\Notifications\ListenerJoinedMail;

class SendListenerJoinedEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $conference;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Conference $conference)
    {
        $this->user = $user;
        $this->conference = $conference;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $conferenceAnnouncers = $this->conference->users()
            ->where('type', '=', User::ANNOUNCER_TYPE)
            ->get();

        $conferenceAnnouncers->each(function ($user) {
            $user->notify(new ListenerJoinedMail($this->user, $this->conference));
        });
    }
}

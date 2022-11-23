<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Conference;

use App\Notifications\AdminDeletedConferenceMail;

class SendAdminDeletedConferenceEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conferenceUsers;
    protected $conferenceTitle;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conferenceUsers, $conferenceTitle)
    {
        $this->conferenceUsers = $conferenceUsers;
        $this->conferenceTitle = $conferenceTitle;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->conferenceUsers->each(function ($user) {
            $user->notify(new AdminDeletedConferenceMail($this->conferenceTitle));
        });
    }
}

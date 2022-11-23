<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Lecture;

use App\Notifications\AdminDeletedLectureMail;

class SendAdminDeletedLectureEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $conference;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lecture $lecture)
    {
        $this->user = $lecture->user;
        $this->conference = $lecture->conference;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new AdminDeletedLectureMail($this->conference));
    }
}

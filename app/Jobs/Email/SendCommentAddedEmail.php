<?php

namespace App\Jobs\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Lecture;

use App\Notifications\CommentAddedMail;

class SendCommentAddedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commentAuthor;
    protected $lecture;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $commentAuthor, Lecture $lecture)
    {
        $this->commentAuthor = $commentAuthor;
        $this->lecture = $lecture;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->lecture->user;
        $user->notify(new CommentAddedMail($this->commentAuthor, $this->lecture));
    }
}

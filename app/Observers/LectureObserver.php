<?php

namespace App\Observers;

use App\Models\Lecture;
use App\Jobs\Email\SendAnnouncerJoinedEmails;
use App\Jobs\Email\SendLectureTimeChangedEmails;

class LectureObserver
{
    /**
     * Handle the Lecture "created" event.
     *
     * @param  \App\Models\Lecture  $lecture
     * @return void
     */
    public function created(Lecture $lecture)
    {
        SendAnnouncerJoinedEmails::dispatch($lecture)->onQueue('emails');
    }

    /**
     * Handle the Lecture "updated" event.
     *
     * @param  \App\Models\Lecture  $lecture
     * @return void
     */
    public function updated(Lecture $lecture)
    {
        if (!$lecture->wasChanged('lecture_start') && !$lecture->wasChanged('lecture_end')) {
            return;
        }

        SendLectureTimeChangedEmails::dispatch($lecture)->onQueue('emails');
    }

    /**
     * Handle the Lecture "deleted" event.
     *
     * @param  \App\Models\Lecture  $lecture
     * @return void
     */
    public function deleted(Lecture $lecture)
    {
        //
    }

    /**
     * Handle the Lecture "restored" event.
     *
     * @param  \App\Models\Lecture  $lecture
     * @return void
     */
    public function restored(Lecture $lecture)
    {
        //
    }

    /**
     * Handle the Lecture "force deleted" event.
     *
     * @param  \App\Models\Lecture  $lecture
     * @return void
     */
    public function forceDeleted(Lecture $lecture)
    {
        //
    }
}

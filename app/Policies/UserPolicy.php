<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conference;
use App\Models\Lecture;
use App\Models\Comment;

use Illuminate\Auth\Access\HandlesAuthorization;

use Carbon\Carbon;

class UserPolicy
{
    use HandlesAuthorization;

    public function store(User $user)
    {
        return false;
    }

    public function update(User $user)
    {
        return false;
    }

    public function destroy(User $user)
    {
        return false;
    }

    public function participate(User $user, Conference $conference)
    {
        return !$conference->isUserAttached($user);
    }

    public function cancelParticipation(User $user, Conference $conference)
    {
        return $conference->isUserAttached($user);
    }

    public function lecturesStore(User $user, $conferenceId)
    {
        if (!$user->isAnnouncer()) {
            return false;
        }

        $existingLecture = Lecture::where([
            ['user_id', '=', $user->id],
            ['conference_id', '=', $conferenceId],
        ])->first();

        return !isset($existingLecture);
    }

    public function lecturesUpdate(User $user, Lecture $lecture)
    {
        return $user->id === $lecture->user_id;
    }

    public function lecturesDestroy(User $user, Lecture $lecture)
    {
        return $user->id === $lecture->user_id;
    }

    public function commentsUpdate(User $user, Comment $comment)
    {
        if ($user->id !== $comment->user_id) {
            return false;
        }

        $now = Carbon::now();
        $updatedAtDate = Carbon::createFromFormat('Y-m-d H:i:s', $comment->updated_at);
        $timeSinceLastUpdate = $now->diffInMinutes($updatedAtDate);

        return $timeSinceLastUpdate < Comment::UPDATE_GAP;
    }
}

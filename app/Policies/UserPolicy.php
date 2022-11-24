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

    public function conferencesStore(User $user)
    {
        return false;
    }

    public function conferencesUpdate(User $user)
    {
        return false;
    }

    public function conferencesDestroy(User $user)
    {
        return false;
    }

    public function conferencesParticipate(User $user, Conference $conference)
    {
        return $user->isListener() && !$conference->isUserAttached($user->id);
    }

    public function conferencesCancelParticipation(User $user, Conference $conference)
    {
        return $user->isListener() && $conference->isUserAttached($user->id);
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

    public function lecturesToFavorites(User $user, Lecture $lecture)
    {
        return !$user->inFavoriteLectures($lecture->id);
    }

    public function lecturesRemoveFromFavorites(User $user, Lecture $lecture)
    {
        return $user->inFavoriteLectures($lecture->id);
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

    public function categoriesStore(User $user)
    {
        return false;
    }

    public function categoriesUpdate(User $user)
    {
        return false;
    }

    public function categoriesDestroy(User $user)
    {
        return false;
    }

    public function exportConferences(User $user)
    {
        return false;
    }

    public function exportLectures(User $user)
    {
        return false;
    }

    public function exportListeners(User $user)
    {
        return false;
    }

    public function exportComments(User $user)
    {
        return false;
    }
}

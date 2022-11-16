<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conference;
use Illuminate\Auth\Access\HandlesAuthorization;

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

    public function storeLecturesRecord(User $user, Conference $conference)
    {
        return $user->isAnnouncer() && !$conference->isUserAttached($user);
    }
}

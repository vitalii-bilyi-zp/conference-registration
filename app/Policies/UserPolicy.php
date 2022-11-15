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
        return $user->isAdmin();
    }

    public function update(User $user)
    {
        return $user->isAdmin();
    }

    public function destroy(User $user)
    {
        return $user->isAdmin();
    }

    public function participate(User $user, Conference $conference)
    {
        return !$conference->isUserAttached($user);
    }

    public function cancelParticipation(User $user, Conference $conference)
    {
        return $conference->isUserAttached($user);
    }
}

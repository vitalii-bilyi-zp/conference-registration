<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserService
{
    public function validateNewEmail(User $user, $newEmail)
    {
        if (!isset($newEmail) || $newEmail === $user->email) {
            return null;
        }

        $conflictUser = User::where('email', $newEmail)->first();
        if (isset($conflictUser)) {
            return [
                'field' => 'email',
                'message' => trans('validation.user_unique_email')
            ];
        }

        return null;
    }

    public function validateNewPassword(User $user, $oldPassword, $newPassword)
    {
        if (!isset($oldPassword) && !isset($newPassword)) {
            return null;
        }

        if(!Hash::check($oldPassword, $user->password)) {
            return [
                'field' => 'old_password',
                'message' => trans('validation.user_old_password')
            ];
        }

        if ($oldPassword === $newPassword) {
            return [
                'field' => 'new_password',
                'message' => trans('validation.user_new_password')
            ];
        }

        return null;
    }
}

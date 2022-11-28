<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Models\User;

class ZoomService
{
    private function getToken()
    {
        $queryParams = [
            'grant_type' => 'account_credentials',
            'account_id' => config('zoom.account_id'),
        ];
        $url = config('zoom.oauth_url') . '?' . http_build_query($queryParams);

        $response = Http::withBasicAuth(config('zoom.client_id'), config('zoom.client_secret'))
            ->post($url);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        return null;
    }

    public function checkUserExistsOtherwiseInvite(User $user)
    {
        if (isset($user->zoom_id)) {
            return true;
        }

        $zoomUser = $this->getUser($user->email);
        if (isset($zoomUser) && $zoomUser['status'] === 'active') {
            $user->update([
                'zoom_id' => $zoomUser['id'],
            ]);
            return true;
        }

        $this->inviteUser($user);

        return false;
    }

    public function getUser($email)
    {
        $token = $this->getToken();
        if (!isset($token)) {
            return null;
        }

        $url = config('zoom.api_url') . '/users/' . $email;
        $response = Http::withToken($token)
            ->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function inviteUser(User $user)
    {
        $token = $this->getToken();
        if (!isset($token)) {
            return null;
        }

        $url = config('zoom.api_url') . '/users';
        $requestBody = [
            'action' => 'create',
            'user_info' => [
                'email' => $user->email,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'type' => 1,
            ],
        ];

        $response = Http::withToken($token)
            ->post($url, $requestBody);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function createMeeting($zoomUserId, $requestBody)
    {
        $token = $this->getToken();
        if (!isset($token)) {
            return null;
        }

        $url = config('zoom.api_url') . '/users/' . $zoomUserId . '/meetings';
        $response = Http::withToken($token)
            ->post($url, $requestBody);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function updateMeeting($zoomMeetingId, $requestBody)
    {
        $token = $this->getToken();
        if (!isset($token)) {
            return false;
        }

        $url = config('zoom.api_url') . '/meetings/' . $zoomMeetingId;
        $response = Http::withToken($token)
            ->patch($url, $requestBody);

        return $response->successful();
    }

    public function deleteMeeting($zoomMeetingId)
    {
        $token = $this->getToken();
        if (!isset($token)) {
            return false;
        }

        $url = config('zoom.api_url') . '/meetings/' . $zoomMeetingId;
        $response = Http::withToken($token)
            ->delete($url);

        return $response->successful();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\Register as AuthRegister;
use App\Http\Requests\Auth\Login as AuthLogin;
use App\Http\Requests\Auth\User as AuthUser;
use App\Http\Requests\Auth\Logout as AuthLogout;

use App\Models\User;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    public function register(AuthRegister $request): JsonResponse
    {
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'type' => $request->type,
            'birthdate' => $request->birthdate,
            'country_id' => $request->country_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken(User::ACCESS_TOKEN)->plainTextToken;

        return $this->respondWithToken($user, $token);
    }

    public function login(AuthLogin $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return $this->respondUnAuthenticated();
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken(User::ACCESS_TOKEN)->plainTextToken;

        return $this->respondWithToken($user, $token);
    }

    public function user(AuthUser $request): JsonResponse
    {
        $user = $request->user();
        $token = $request->bearerToken();

        return $this->respondWithToken($user, $token);
    }

    public function logout(AuthLogout $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->respondWithSuccess();
    }

    private function respondWithToken(Authenticatable $user, $token): JsonResponse
    {
        $conference_ids = $user->conferences->map(function ($item) {
            return $item->id;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess([
            'user' => [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'type' => $user->type,
                'birthdate' => $user->birthdate,
                'country' => $user->country,
                'phone' => $user->phone,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'conference_ids' => $conference_ids,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }
}

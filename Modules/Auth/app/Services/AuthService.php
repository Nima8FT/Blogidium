<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Services\Contracts\AuthServiceInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): User
    {
        return User::create($data);
    }

    public function login(array $data): bool|array
    {
        if (! $token = Auth::attempt($data)) {
            return false;
        }

        return [
            'user' => Auth::user(),
            'token' => $token,
        ];
    }

    public function logout(): bool
    {
        $token = JWTAuth::getToken();
        $response = JWTAuth::invalidate($token);
        if ($response) {
            return true;
        }

        return false;
    }

    public function deleteAccount(): bool
    {
        $user = Auth::user();
        $user->delete();
        if (! $user) {
            return false;
        }

        return true;
    }
}

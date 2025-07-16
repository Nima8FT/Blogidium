<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Services\Contracts\AuthServiceInterface;

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
}

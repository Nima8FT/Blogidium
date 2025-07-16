<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Modules\Auth\Services\Contracts\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): User
    {
        return User::create($data);
    }
}

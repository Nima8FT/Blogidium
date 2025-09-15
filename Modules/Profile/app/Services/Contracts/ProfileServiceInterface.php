<?php

namespace Modules\Profile\Services\Contracts;

use App\Models\User;

interface ProfileServiceInterface
{
    public function updateProfile(array $data, User $user): bool;
}

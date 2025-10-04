<?php

namespace Modules\RolePermission\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRolePolicy
{
    use HandlesAuthorization;

    public function addRole(User $user) {
        return $user->hasRole('super-admin');
    }

    public function removeRole(User $user) {
        return $user->hasRole('super-admin');
    }
}

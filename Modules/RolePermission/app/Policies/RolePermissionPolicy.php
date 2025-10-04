<?php

namespace Modules\RolePermission\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePermissionPolicy
{
    use HandlesAuthorization;

    public function addPermission(User $user) {
        return $user->hasRole('super-admin');
    }

    public function removePermission(User $user) {
        return $user->hasRole('super-admin');
    }
}

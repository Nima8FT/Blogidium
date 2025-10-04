<?php

namespace Modules\RolePermission\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('super-admin');
    }

    public function view(User $user)
    {
        return $user->hasRole('super-admin');
    }

    public function index(User $user)
    {
        return $user->hasRole('super-admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('super-admin');
    }

    public function update(User $user)
    {
        return $user->hasRole('super-admin');
    }

    public function destroy(User $user)
    {
        return $user->hasRole('super-admin');
    }
}

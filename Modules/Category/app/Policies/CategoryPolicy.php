<?php

namespace Modules\Category\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(['super-admin','admin','premium user','normal user']);
    }

    public function view(User $user)
    {
        return $user->hasRole(['super-admin','admin','premium user','normal user']);
    }

    public function create(User $user)
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function update(User $user)
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function destroy(User $user)
    {
        return $user->hasRole(['super-admin', 'admin']);
    }
}

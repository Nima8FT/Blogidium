<?php

namespace Modules\Article\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole(['super-admin','admin','premium user','normal user', 'guest']);
    }

    public function view(User $user)
    {
        return $user->hasRole(['super-admin','admin','premium user','normal user','guest']);
    }

    public function create(User $user)
    {
        return $user->hasRole(['super-admin', 'admin','premium user','normal user']);
    }

    public function update(User $user)
    {
        return $user->hasRole(['super-admin', 'admin','premium user','normal user']);
    }

    public function destroy(User $user)
    {
        return $user->hasRole(['super-admin', 'admin','premium user','normal user']);
    }

    public function viewPremium(User $user) {
        return $user->hasRole(['super-admin', 'admin','premium user']);
    }
}

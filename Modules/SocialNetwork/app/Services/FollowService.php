<?php

namespace Modules\SocialNetwork\Services;

use App\Models\User;
use Modules\SocialNetwork\Services\Contracts\FollowServiceInterface;

class FollowService implements FollowServiceInterface
{
    public function follow(User $user): bool
    {
        $follower = auth()->user();
        if ($this->isSelf($user, $follower)) {
            return false;
        }
        if ($follower->followings->contains($user->id)) {
            return false;
        }
        $follower->followings()->attach($user);

        return true;
    }

    public function unfollow(User $user): bool
    {
        $follower = auth()->user();
        if ($this->isSelf($user, $follower)) {
            return false;
        }
        if ($follower->followings->contains($user->id)) {
            $follower->followings()->detach($user);

            return true;
        }

        return false;
    }

    public function followings()
    {
        $user = auth()->user();

        return $user->followings;
    }

    public function followers()
    {
        $user = auth()->user();

        return $user->followers;
    }

    public function isSelf($user, $follower): bool
    {
        return $user->id === $follower->id;
    }
}

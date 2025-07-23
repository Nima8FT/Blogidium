<?php

namespace Modules\SocialNetwork\Services\Contracts;

use App\Models\User;

interface FollowServiceInterface
{
    public function follow(User $user);

    public function unfollow(User $user);

    public function followings();

    public function followers();

    public function isSelf($user, $follower);
}

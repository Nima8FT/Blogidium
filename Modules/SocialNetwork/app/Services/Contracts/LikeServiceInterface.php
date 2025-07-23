<?php

namespace Modules\SocialNetwork\Services\Contracts;

use Modules\Article\Models\Article;

interface LikeServiceInterface
{
    public function setLikeStatus(Article $article, bool $status);
}

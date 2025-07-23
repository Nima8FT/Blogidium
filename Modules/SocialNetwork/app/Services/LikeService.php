<?php

namespace Modules\SocialNetwork\Services;

use Modules\Article\Models\Article;
use Modules\SocialNetwork\Services\Contracts\LikeServiceInterface;

class LikeService implements LikeServiceInterface
{
    public function setLikeStatus(Article $article, bool $status)
    {
        $user = auth()->user();
        if ($user->likedArticles()->where('article_id', $article->id)->exists()) {
            $user->likedArticles()->updateExistingPivot($article->id, ['like' => $status]);
        } else {
            $user->likedArticles()->attach($article->id, ['like' => $status]);
        }

        return [
            'username' => $user->username,
            'title' => $article->title,
            'like' => $status ? 'like' : 'dislike',
        ];
    }
}

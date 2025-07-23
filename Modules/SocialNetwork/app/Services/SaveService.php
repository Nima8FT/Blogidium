<?php

namespace Modules\SocialNetwork\Services;

use App\Models\User;
use Modules\Article\Models\Article;
use Modules\SocialNetwork\Services\Contracts\SaveServiceInterface;

class SaveService implements SaveServiceInterface
{
    public function saveArticle(Article $article)
    {
        $user = auth()->user();
        if ($user->savedArticles()->where('article_id', $article->id)->exists()) {
            return $this->buildResponse($user, $article, 'already_saved');
        }

        $user->savedArticles()->attach($article);

        return $this->buildResponse($user, $article, 'saved');
    }

    public function unsaveArticle(Article $article)
    {
        $user = auth()->user();
        if (! $user->savedArticles()->where('article_id', $article->id)->exists()) {
            return $this->buildResponse($user, $article, 'not_saved');
        }

        $user->savedArticles()->detach($article);

        return $this->buildResponse($user, $article, 'unsaved');
    }

    private function buildResponse(User $user, Article $article, string $status)
    {
        return [
            'username' => $user->username,
            'title' => $article->title,
            'status' => $status,
        ];
    }
}

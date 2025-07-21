<?php

namespace Modules\Article\Services;

use App\Models\User;
use Modules\Article\Models\Article;
use Modules\Article\Services\Contracts\ArticleServiceInterface;

class ArticleService implements ArticleServiceInterface
{
    public function create(array $data, User $user)
    {
        $data['author_id'] = $user->id;

        return Article::create($data);
    }

    public function update(Article $article, array $data, User $user)
    {
        if ($article->author_id === $user->id) {
            return $article->update($data);
        }

        return false;
    }

    public function delete(Article $article, User $user)
    {
        if ($article->author_id === $user->id) {
            return $article->delete();
        }

        return false;
    }
}

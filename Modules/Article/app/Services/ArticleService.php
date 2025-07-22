<?php

namespace Modules\Article\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Article\Models\Article;
use Modules\Article\Services\Contracts\ArticleServiceInterface;

class ArticleService implements ArticleServiceInterface
{
    public function create(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $tags_id = $data['tags'] ?? [];
            $data['author_id'] = $user->id;
            $article = Article::create($data);
            if (! empty($tags_id)) {
                $article->tags()->sync($tags_id);
            }

            return $article;
        });
    }

    public function update(Article $article, array $data, User $user)
    {
        if ($article->author_id !== $user->id) {
            return false;
        }

        return DB::transaction(function () use ($article, $data) {
            $tags_id = $data['tags'] ?? [];
            unset($data['tags']);
            $article->update($data);
            if (! empty($tags_id)) {
                $article->tags()->sync($tags_id);
            }

            return $article;
        });
    }

    public function delete(Article $article, User $user)
    {
        if ($article->author_id === $user->id) {
            return $article->delete();
        }

        return false;
    }
}

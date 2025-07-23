<?php

namespace Modules\SocialNetwork\Services\Contracts;

use Modules\Article\Models\Article;

interface SaveServiceInterface
{
    public function saveArticle(Article $article);

    public function unsaveArticle(Article $article);
}

<?php

namespace Modules\Article\Services\Contracts;

use App\Models\User;
use Modules\Article\Models\Article;

interface ArticleServiceInterface
{
    public function create(array $data, User $user);

    public function update(Article $article, array $data, User $user);

    public function delete(Article $article, User $user);
}

<?php

namespace Modules\SocialNetwork\Services\Contracts;

use Modules\Article\Models\Article;
use Modules\SocialNetwork\Models\Comment;

interface CommentServiceInterface
{
    public function create(Article $article, array $data);

    public function update(Article $article, array $data, Comment $comment);

    public function delete(Article $article, Comment $comment);
}

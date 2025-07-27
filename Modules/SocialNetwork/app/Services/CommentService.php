<?php

namespace Modules\SocialNetwork\Services;

use Modules\Article\Models\Article;
use Modules\SocialNetwork\Models\Comment;
use Modules\SocialNetwork\Services\Contracts\CommentServiceInterface;

class CommentService implements CommentServiceInterface
{
    private function getAuthenticatedUser()
    {
        return auth()->user();
    }

    private function isValidParentComment(Article $article, array $data)
    {
        if (! empty($data['parent_id'])) {
            $parent = Comment::where('id', $data['parent_id'])
                ->where('article_id', $article->id)
                ->first();

            if (! $parent) {
                return false;
            }
        }

        return true;
    }

    public function create($article, array $data)
    {
        $user = $this->getAuthenticatedUser();
        $data['user_id'] = $user->id;
        $data['article_id'] = $article->id;
        $parent = $this->isValidParentComment($article, $data);
        if ($parent) {
            return Comment::create($data);
        }

        return false;
    }

    public function update(Article $article, array $data, Comment $comment)
    {
        $user = $this->getAuthenticatedUser();
        $parent = $this->isValidParentComment($article, $data);
        if (($user->id === $comment->user_id) && ($article->id === $comment->article_id) && $parent) {
            return $comment->update($data);
        }

        return false;
    }

    public function delete(Article $article, Comment $comment)
    {
        $user = $this->getAuthenticatedUser();
        if (($user->id === $comment->user_id) && ($article->id === $comment->article_id)) {
            return $comment->delete();
        }

        return false;
    }
}

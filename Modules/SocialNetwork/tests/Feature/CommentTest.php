<?php

namespace Modules\SocialNetwork\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Modules\SocialNetwork\Models\Comment;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_comments()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'content' => 'test comment',
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)->getJson(route('api.comments.index', $article->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'content',
                    'article' => [
                        'id',
                        'title',
                    ],
                    'user' => [
                        'id',
                        'username',
                    ],
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_create_comment()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->postJson(route('api.comments.store', $article->id), [
                'content' => 'test comment',
                'article_id' => $article->id,
                'user_id' => $user->id,
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'content',
                'article' => [
                    'id',
                    'title',
                ],
                'user' => [
                    'id',
                    'username',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_create_comment_wrong_article_id()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->postJson(route('api.comments.store', 999), [
                'content' => 'test comment',
                'article_id' => 999,
                'user_id' => $user->id,
            ]);

        $response->assertStatus(404);
    }

    public function test_it_can_show_comment()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'content' => 'test comment',
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->getJson(route('api.comments.show', [$article->id, $comment->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'content',
                'article' => [
                    'id',
                    'title',
                ],
                'user' => [
                    'id',
                    'username',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_update_comment()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'content' => 'test comment',
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->getJson(route('api.comments.update', [$article->id, $comment->id]), [
                'content' => 'test comment update',
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'content',
                'article' => [
                    'id',
                    'title',
                ],
                'user' => [
                    'id',
                    'username',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_delete_comment()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'content' => 'test comment',
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->getJson(route('api.comments.destroy', [$article->id, $comment->id]));

        $response->assertStatus(200);
    }
}

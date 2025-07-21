<?php

namespace Modules\Article\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_it_can_list_articles(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('api.articles.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'author' => [
                        'author_name',
                    ],
                    'category' => [
                        'category_name',
                        'category_slug',
                    ],
                    'is_published',
                    'published_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_cannot_list_articles_with_fake_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->getJson(route('api.articles.index'));

        $response->assertStatus(401);
    }

    public function test_it_can_create_article(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.articles.store'), [
            'title' => 'test title',
            'content' => 'test content',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'slug',
                'content',
                'author' => [
                    'author_name',
                ],
                'category' => [
                    'category_name',
                    'category_slug',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_create_article_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.articles.store'), [
            'title' => 'test title',
            'content' => 'test content',
            'category_id' => 9999,
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_show_article(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $token = JWTAuth::fromUser($user);

        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('api.articles.show', $article->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'slug',
                'content',
                'author' => [
                    'author_name',
                ],
                'category' => [
                    'category_name',
                    'category_slug',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_show_article_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('api.articles.show', 999));

        $response->assertStatus(404);
    }

    public function test_it_can_update_article(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $token = JWTAuth::fromUser($user);

        $article = Article::factory()->create([
            'title' => 'test title',
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson(route('api.articles.update', $article->id), [
            'title' => 'title',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'slug',
                'content',
                'author' => [
                    'author_name',
                ],
                'category' => [
                    'category_name',
                    'category_slug',
                ],
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_update_article_with_invalid_token(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $token = JWTAuth::fromUser($user);

        $article = Article::factory()->create([
            'title' => 'test title',
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->putJson(route('api.articles.update', $article->id), [
            'title' => 'test title',
        ]);

        $response->assertStatus(401);
    }

    public function test_it_can_delete_article(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $token = JWTAuth::fromUser($user);

        $article = Article::factory()->create([
            'title' => 'test title',
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson(route('api.articles.destroy', $article->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }

    public function test_it_cannot_delete_article_with_invalid_id(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $token = JWTAuth::fromUser($user);

        $article = Article::factory()->create([
            'title' => 'test title',
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson(route('api.articles.destroy', 999));

        $response->assertStatus(404);
    }
}

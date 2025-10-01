<?php

namespace Modules\Search\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_meili_search()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();

        Article::factory()->count(1)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $article = Article::factory()->create([
            'title' => 'MeiliSearch',
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $article->searchable();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.search'), [
                'search' => 'MeiliSearch',
            ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => 'MeiliSearch',
        ]);

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
                    'tags' => [],
                    'likes',
                    'dislikes',
                    'is_published',
                    'published_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_not_meili_search()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();

        Article::factory()->count(2)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.search'), [
                'search' => 'FooSearch',
            ]);

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'message' => 'Search Article retrieved successfully.',
            'data' => [],
        ]);
    }
}

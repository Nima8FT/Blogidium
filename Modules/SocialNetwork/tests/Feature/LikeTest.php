<?php

namespace Modules\SocialNetwork\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_it_can_like_article()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('api.like', $article->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'username',
                'title',
                'like',
            ],
        ]);
    }

    public function test_user_it_can_dislike_article()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('api.dislike', $article->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'username',
                'title',
                'like',
            ],
        ]);
    }

    public function test_fake_user_it_cannot_like_article()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->postJson(route('api.dislike', $article->id));

        $response->assertStatus(401);
    }
}

<?php

namespace Modules\SocialNetwork\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_user_save_article()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('api.save', $article->id));

        $response->assertStatus(200);

        $this->assertDatabaseHas('saves', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'username',
                'title',
                'status',
            ],
        ]);
    }

    public function test_it_can_user_unsave_article()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('api.unsave', $article->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('saves', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'username',
                'title',
                'status',
            ],
        ]);
    }

    public function test_it_cannot_user_save_article_without_token()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer fake-token'
        )->postJson(route('api.unsave', $article->id));

        $response->assertStatus(401);
    }
}

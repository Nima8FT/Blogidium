<?php

namespace Modules\Profile\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_profile()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)->getJson(route('api.profile.show'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'name',
                'email',
                'username',
                'phone',
                'photo',
            ],
        ]);
    }

    public function test_cannot_show_profile()
    {
        $response = $this->withHeader('Authorization', 'Bearer fake-token')->getJson(route('api.profile.show'));

        $response->assertStatus(401);
    }

    public function test_can_update_profile()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.profile.update'), [
                'username' => 'testUsername',
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'name',
                'email',
                'username',
                'phone',
                'photo',
            ],
        ]);
    }

    public function test_cannot_update_profile()
    {
        $response = $this->withHeader('Authorization', 'Bearer fake-token')->postJson(route('api.profile.update'));

        $response->assertStatus(401);
    }

    public function test_can_get_library()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $articles = Article::factory(10)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $user->savedArticles()->attach($articles->pluck('id')->toArray());
        $this->assertDatabaseCount('saves', 10);

        $response = $this
            ->withHeaders(['Authorization' => 'Bearer '.$token])
            ->getJson(route('api.profile.library'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'image',
                    'author_id',
                    'category_id',
                    'is_published',
                    'published_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'pivot' => [
                        'user_id',
                        'article_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    public function test_can_get_my_articles()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $category = Category::factory()->create();
        $articles = Article::factory(10)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.profile.my-articles'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                [
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
}

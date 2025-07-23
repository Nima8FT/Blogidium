<?php

namespace Modules\SocialNetwork\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_it_can_follow_a_user()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.follow', $user->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'username',
            ],
        ]);
    }

    public function test_it_cannot_follow_a_user_to_yourself()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.follow', $follower->id));

        $response->assertStatus(400);
    }

    public function test_it_cannot_follow_a_user_with_invalid_id()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.follow', 999));

        $response->assertStatus(404);
    }

    public function test_it_can_unfollow_a_user()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);
        $user = User::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.follow', $user->id));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.unfollow', $user->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'username',
            ],
        ]);
    }

    public function test_it_can_unfollow_a_user_to_yourself()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.unfollow', $follower->id));

        $response->assertStatus(400);
    }

    public function test_it_cannot_unfollow_a_user_not_following()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.unfollow', $user->id));

        $response->assertStatus(400);
    }

    public function test_it_can_list_followings_user()
    {
        $follower = User::factory()->create();
        $token = JWTAuth::fromUser($follower);
        $user = User::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.follow', $user->id));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('api.followings'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'username',
                ],
            ],
        ]);
    }

    public function test_it_can_list_followers_user()
    {
        $follower = User::factory()->create();
        $followerToken = JWTAuth::fromUser($follower);
        $user = User::factory()->create();
        $userToken = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken,
        ])->postJson(route('api.follow', $follower->id));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$followerToken,
        ])->getJson(route('api.followers'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'username',
                ],
            ],
        ]);
    }
}

<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.logout'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('api.logout'));

        $response->assertStatus(401);
    }

    public function test_user_cannot_logout_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->postJson(route('api.logout'));

        $response->assertStatus(401);
    }
}

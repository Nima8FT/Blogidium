<?php

namespace Modules\Profile\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

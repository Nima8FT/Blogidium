<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_can_login_successfully(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson(route('login'), [
            'login' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$response->json('token'),
        ])->getJson(route('profile'))->assertOk();

        $response->assertOk();

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'username',
                'email',
            ],
            'token',
        ]);
    }

    public function test_user_cannot_access_profile_with_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->getJson(route('profile'));

        $response->assertStatus(401);
    }

    public function test_user_cannot_login_with_invalid_data(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson(route('login'), [
            'login' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }
}

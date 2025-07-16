<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_can_register_successfully(): void
    {
        $response = $this->post(route('api.register'), [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

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
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);
    }

    public function test_user_cannot_register_invalid_data(): void
    {
        $response = $this->postJson(route('api.register'), [
            'name' => '',
            'username' => 123,
            'email' => 'bad-email',
            'password' => '123',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'username', 'email', 'password']);
    }
}

<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_deleted_successfully(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson(route('api.delete-account'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $this->assertSoftDeleted('users', [
            'email' => $user->email,
        ]);
    }

    public function test_user_cannot_delete_account_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->deleteJson(route('api.delete-account'));
        $response->assertStatus(401);
    }
}

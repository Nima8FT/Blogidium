<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Auth\Models\PhoneVerification;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_notification_sms()
    {
        Http::fake([
            'https://console.melipayamak.com/*' => Http::response([
                'code' => '654321',
                'status' => 'success',
            ], 200),
        ]);

        $user = User::factory()->create([
            'phone' => '09138014541',
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('phone.send-code'));

        $response->assertStatus(200);

        $this->assertDatabaseHas('phone_verifications', [
            'user_id' => $user->id,
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user' => [
                    'username',
                    'phone',
                ],
                'code',
                'expires_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_verify_code_successfully()
    {
        $user = User::factory()->create([
            'phone' => '09120000000',
        ]);

        $token = JWTAuth::fromUser($user);

        $code = '123456';
        PhoneVerification::create([
            'user_id' => $user->id,
            'token' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('phone.verify-code'), [
            'code' => $code,
        ]);

        $response->assertStatus(200);

        $this->assertNotNull($user->fresh()->phone_verified_at);

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
    }
}

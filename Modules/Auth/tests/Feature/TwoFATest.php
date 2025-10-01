<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Auth\Models\UserTwoFA;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TwoFATest extends TestCase
{
    use RefreshDatabase;

    public function test_enable_2fa(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('2fa.enable'), []);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'secret',
                'qr_code',
            ],
        ]);

        $this->assertDatabaseHas('user_twofa', [
            'user_id' => $user->id,
            'enabled' => true,
        ]);
    }

    public function test_enable_2fa_fail_fake_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
        ])->postJson(route('2fa.enable'), []);

        $response->assertStatus(401);
    }

    public function test_disable_2fa(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $user_twofa = UserTwoFA::create([
            'user_id' => $user->id,
            'secret' => 'fake-secret',
            'enabled' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('2fa.disable'), []);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $this->assertDatabaseHas('user_twofa', [
            'user_id' => $user->id,
            'enabled' => false,
        ]);
    }

    public function test_disable_2fa_fail(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('2fa.disable'), []);

        $response->assertStatus(500);
    }

    public function test_verify_2fa(): void
    {
        $user = User::factory()->create();
        $secret = (new Google2FA)->generateSecretKey();

        $twoFA = UserTwoFA::create([
            'user_id' => $user->id,
            'secret_code' => $secret,
            'enabled' => true,
            'temp_token' => hash('sha256', Str::random(64)),
        ]);

        $validOtp = (new Google2FA)->getCurrentOtp($secret);

        $response = $this->postJson(route('2fa.verify'), [
            'token' => $twoFA->temp_token,
            'otp' => str_pad($validOtp, 6, '0', STR_PAD_LEFT),
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
            ],
            'token',
        ]);
    }

    public function test_verify_2fa_fail(): void
    {
        $user = User::factory()->create();
        $secret = (new Google2FA)->generateSecretKey();

        $twoFA = UserTwoFA::create([
            'user_id' => $user->id,
            'secret_code' => $secret,
            'enabled' => true,
            'temp_token' => hash('sha256', Str::random(64)),
        ]);

        $response = $this->postJson(route('2fa.verify'), [
            'token' => $twoFA->temp_token,
            'otp' => 123456,
        ]);

        $response->assertStatus(400);
    }
}

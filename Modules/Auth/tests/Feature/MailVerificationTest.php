<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_notification_email_and_verification_email()
    {
        $user = User::factory()->unverified()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('mail.notification'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson($verificationUrl);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }

    public function test_wrong_url_for_verification_email()
    {
        $user = User::factory()->unverified()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson(route('mail.notification'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token
        )->postJson('wrong-url');

        $response->assertStatus(404);
    }
}

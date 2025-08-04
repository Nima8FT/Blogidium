<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_notification_forgot_password()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => ['email'],
        ]);

        Notification::assertSentTo(
            [$user], ResetPassword::class
        );
    }

    public function test_it_can_reset_password()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('password.email'), [
            'email' => $user->email,
        ])->assertStatus(200);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $newPassword = 'new-secure-password';

        $this->postJson(route('password.reset'), [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertStatus(200);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }
}

<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Modules\Auth\Services\SocialLoginService;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_login_with_github(): void
    {
        $fakeUser = User::factory()->make([
            'id' => 1,
            'name' => 'Fake User',
            'email' => 'fakeuser@example.com',
            'username' => 'fakeuser',
        ]);

        $fakeToken = 'fake-jwt-token-123456';

        $mockService = Mockery::mock(SocialLoginService::class);

        $mockService->shouldReceive('getGithubAccessToken')
            ->once()
            ->andReturn('fake_github_access_token');

        $mockService->shouldReceive('socialLogin')
            ->once()
            ->andReturn([
                'user' => $fakeUser,
                'token' => $fakeToken,
            ]);

        $this->app->instance(SocialLoginService::class, $mockService);

        $response = $this->postJson(route('social.login', ['provider' => 'github']), [
            'code' => 'fake_code_123',
        ]);

        $response->assertStatus(200);

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

    public function test_it_can_login_with_google(): void
    {
        $fakeUser = User::factory()->create();
        $fakeToken = 'fake-jwt-token-123456';

        $mockService = Mockery::mock(SocialLoginService::class);

        $mockService->shouldReceive('getGoogleAccessToken')->once()->andReturn('fake_google_access_token');
        $mockService->shouldReceive('socialLogin')->once()->andReturn([
            'user' => $fakeUser,
            'token' => $fakeToken,
        ]);

        $this->app->instance(SocialLoginService::class, $mockService);

        $response = $this->postJson(route('social.login', ['provider' => 'google']), [
            'code' => $fakeToken,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'username',
            ],
            'token',
        ]);
    }
}

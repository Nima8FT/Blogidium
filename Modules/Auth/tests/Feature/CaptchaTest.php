<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Modules\Auth\Services\CaptchaService;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    public function test_verify_token_success_captcha(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $service = new CaptchaService;

        $result = $service->verifyToken('fake-token');

        $this->assertTrue($result);
    }

    public function test_verify_token_failure_captcha(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
            ], 200),
        ]);

        $service = new CaptchaService;

        $result = $service->verifyToken('fake-token');

        $this->assertFalse($result);
    }
}

<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Http;
use Modules\Auth\Services\Contracts\CaptchaServiceInterface;

class CaptchaService implements CaptchaServiceInterface
{
    public function verifyToken(string $token): bool
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $token,
        ]);

        $result = $response->json();

        return $result['success'] ?? false;
    }
}

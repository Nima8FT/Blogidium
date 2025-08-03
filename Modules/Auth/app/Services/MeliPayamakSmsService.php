<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Http;
use Modules\Auth\Services\Contracts\SmsSenderInterface;

class MeliPayamakSmsService implements SmsSenderInterface
{
    public function sendVerificationCode(string $phone): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://console.melipayamak.com/api/send/otp/98b162aeefe44095981fdc3d76adf3bb', [
            'to' => $phone,
        ]);

        return $response->json();
    }
}

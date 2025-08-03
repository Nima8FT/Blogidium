<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Modules\Auth\Models\PhoneVerification;
use Modules\Auth\Services\Contracts\PhoneVerificationInterface;
use Modules\Auth\Services\Contracts\SmsSenderInterface;

class PhoneVerificationService implements PhoneVerificationInterface
{
    public function __construct(private SmsSenderInterface $smsSender) {}

    public function send(): PhoneVerification|bool
    {
        $user = auth()->user();
        $response = $this->smsSender->sendVerificationCode($user->phone);

        $phoneVerification = PhoneVerification::updateOrCreate([
            'user_id' => $user->id,
            'token' => $response['code'],
            'expires_at' => now()->addMinutes(5),
        ]);

        if ($phoneVerification) {
            return $phoneVerification;
        }

        return false;
    }

    public function verify(array $code): User|bool
    {
        $user = auth()->user();
        $phoneVerification = PhoneVerification::where('user_id', $user->id)->where('token', $code)->first();
        if ($phoneVerification) {
            $user->update([
                'phone_verified_at' => now(),
            ]);

            return $user;
        }

        return false;
    }
}

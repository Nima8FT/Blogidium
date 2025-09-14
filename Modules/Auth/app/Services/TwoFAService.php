<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Modules\Auth\Models\UserTwoFA;
use Modules\Auth\Services\Contracts\TwoFAServiceInterface;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tymon\JWTAuth\Facades\JWTAuth;

class TwoFAService implements TwoFAServiceInterface
{
    public function enableTwoFA(User $user, Google2FA $google2fa): array
    {
        $secret = $google2fa->generateSecretKey();

        UserTwoFA::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret_code' => $secret,
                'enabled' => true,
            ]
        );

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCodeImage = QrCode::format('png')->size(200)->generate($qrCodeUrl);

        return [
            'secret' => $secret,
            'qr_code' => 'data:image/png;base64,'.base64_encode($qrCodeImage),
        ];
    }

    public function disableTwoFA(User $user): bool
    {
        $user_twofa = UserTwoFA::where('user_id', $user->id)->first();
        $user_twofa->update([
            'enabled' => false,
        ]);

        return true;
    }

    public function verifyTwoFA(array $data, Google2FA $google2fa): array|false
    {
        $userTwoFA = UserTwoFA::where('temp_token', $data['token'])->first();
        $user = User::find($userTwoFA->user_id);
        $secret = $userTwoFA->secret_code;

        $valid = $google2fa->verifyKey($secret, $data['otp']);

        if ($valid) {
            $token = JWTAuth::fromUser($user);

            return [
                'user' => $user,
                'token' => $token,
            ];
        }

        return false;
    }

    public function checkTwoFA(User $user): UserTwoFA|false
    {
        $userTwoFA = UserTwoFA::where('user_id', $user->id)->where('enabled', true)->first();
        if ($userTwoFA) {
            $tempToken = hash('sha256', Str::random(64));
            $userTwoFA->update([
                'temp_token' => $tempToken,
            ]);

            return $userTwoFA;
        } else {
            return false;
        }
    }
}

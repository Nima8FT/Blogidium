<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Auth\Services\Contracts\ResetPasswordInterface;

class ResetPasswordService implements ResetPasswordInterface
{
    public function forgotPassword(array $data): string
    {
        $status = Password::sendResetLink(['email' => $data['email']]);

        return $status;
    }

    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->update([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ]);
            }
        );

        return $status;
    }
}

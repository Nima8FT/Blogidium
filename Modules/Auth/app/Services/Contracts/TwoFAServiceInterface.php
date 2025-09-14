<?php

namespace Modules\Auth\Services\Contracts;

use App\Models\User;
use Modules\Auth\Models\UserTwoFA;
use PragmaRX\Google2FA\Google2FA;

interface TwoFAServiceInterface
{
    public function enableTwoFA(User $user, Google2FA $google2fa): array;

    public function disableTwoFA(User $user): bool;

    public function verifyTwoFA(array $data, Google2FA $google2fa): array|false;

    public function checkTwoFA(User $user): UserTwoFA|false;
}

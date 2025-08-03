<?php

namespace Modules\Auth\Services\Contracts;

use App\Models\User;
use Modules\Auth\Models\PhoneVerification;

interface PhoneVerificationInterface
{
    public function send(): PhoneVerification|bool;

    public function verify(array $code): User|bool;
}

<?php

namespace Modules\Auth\Services\Contracts;

interface CaptchaServiceInterface
{
    public function verifyToken(string $token): bool;
}

<?php

namespace Modules\Auth\Services\Contracts;

interface SmsSenderInterface
{
    public function sendVerificationCode(string $phone): array;
}

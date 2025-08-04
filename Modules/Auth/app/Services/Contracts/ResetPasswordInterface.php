<?php

namespace Modules\Auth\Services\Contracts;

interface ResetPasswordInterface
{
    public function forgotPassword(array $data): string;

    public function resetPassword(array $data): string;
}

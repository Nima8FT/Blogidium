<?php

namespace Modules\Auth\Services;

class LoginFieldDetector
{
    public function detect(string $input): string
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match("/^09\d{9}$/", $input)) {
            return 'phone';
        } else {
            return 'username';
        }
    }
}

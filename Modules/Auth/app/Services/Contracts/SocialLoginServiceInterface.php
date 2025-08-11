<?php

namespace Modules\Auth\Services\Contracts;

interface SocialLoginServiceInterface
{
    public function getGithubAccessToken(string $code): string;

    public function getGoogleAccessToken(string $code): string;

    public function socialLogin(string $provider, string $accessToken): array;
}

<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Models\LinkedSocialAccount;
use Modules\Auth\Services\Contracts\SocialLoginServiceInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialLoginService implements SocialLoginServiceInterface
{
    public function getGithubAccessToken(string $code): string
    {
        // route for front click on github login https://github.com/login/oauth/authorize?client_id=Ov23lihiSk0LGX7lhO3p&redirect_uri=http://localhost/auth/github/callback
        // get access token from github

        $tokenResponse = Http::asForm()
            ->withHeaders(['Accept' => 'application/json'])
            ->post('https://github.com/login/oauth/access_token', [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => $code,
                'redirect_uri' => config('services.github.redirect'),
            ]);

        $accessToken = $tokenResponse->json('access_token');

        if (! is_string($accessToken) || empty($accessToken)) {
            throw new \Exception('Failed to get Github access token.');
        }

        return $accessToken;
    }

    public function getGoogleAccessToken(string $code): string
    {
        // route for front click on google login
        // route in browser = https://accounts.google.com/o/oauth2/v2/auth?client_id=65352905803-f20nfe7mkrvqv230csoq9c44oksjs73h.apps.googleusercontent.com&redirect_uri=http://localhost/auth/google/callback&response_type=code&scope=email%20profile
        // get access token from google

        $tokenResponse = Http::asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect'),
            ]);

        $accessToken = $tokenResponse->json('access_token');

        if (! is_string($accessToken) || empty($accessToken)) {
            throw new \Exception('Failed to get Google access token.');
        }

        return $accessToken;
    }

    public function socialLogin(string $provider, string $accessToken): array
    {
        $providerUser = Socialite::driver($provider)->userFromToken($accessToken);

        $linkedAccount = LinkedSocialAccount::where('provider_id', $providerUser->getId())
            ->where('provider_name', $provider)
            ->first();

        if ($linkedAccount) {
            $user = $linkedAccount->user;
        } else {
            $user = User::where('email', $providerUser->getEmail())
                ->orWhere('username', $providerUser->getNickname())
                ->first();

            if (! $user) {
                $user = DB::transaction(function () use ($providerUser, $provider) {
                    $user = User::create([
                        'name' => $providerUser->getName(),
                        'email' => $providerUser->getEmail(),
                        'username' => $providerUser->getNickname() ? $providerUser->getNickname() : $providerUser->getEmail(),
                        'profile_photo' => $providerUser->getAvatar(),
                        'password' => Str::uuid()->toString(),
                    ]);

                    LinkedSocialAccount::create([
                        'provider_id' => $providerUser->getId(),
                        'provider_name' => $provider,
                        'user_id' => $user->id,
                    ]);

                    return $user;
                });
            }
        }

        $jwt = JWTAuth::fromUser($user);

        return [
            'token' => $jwt,
            'user' => $user,
        ];
    }
}

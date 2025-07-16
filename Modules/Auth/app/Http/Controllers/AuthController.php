<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Services\Contracts\AuthServiceInterface;
use Modules\Auth\Services\LoginFieldDetector;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\UserResource;

class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService,
        private LoginFieldDetector $loginFieldDetector
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->register($data);

        return ResponseBuilder::success(
            new UserResource($user),
            'User created successfully.'
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = [
            $this->loginFieldDetector->detect($request->input('login')) => $request->validated()['login'],
            'password' => $request->validated()['password'],
        ];

        $result = $this->authService->login($credentials);

        if (! $result) {
            return ResponseBuilder::error('Invalid credentials. Please check your username/email/phone and password.');
        }

        return ResponseBuilder::success(
            new UserResource($result['user']),
            'User logged in successfully.',
            $result['token']
        );
    }

    public function profile()
    {
        return 'i am in';
    }
}

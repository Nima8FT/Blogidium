<?php

namespace Modules\Auth\Services\Contracts;

interface AuthServiceInterface
{
    public function register(array $data);

    public function login(array $data);

    public function logout();

    public function deleteAccount();

    public function getUser();
}

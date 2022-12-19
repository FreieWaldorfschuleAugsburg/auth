<?php

namespace App\Auth;

use App\Auth\User\UserService;
use App\models\AccessToken;
use App\models\IdToken;
use App\models\IdTokenUser;
use App\models\RefreshToken;
use Illuminate\Support\Facades\Log;
use LdapRecord\Models\ActiveDirectory\User;

class TokenService
{

    protected UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function generateIdToken(string $clientId, string $distinguishedName): IdToken
    {
        $idTokenUser = $this->userService->getUserData($distinguishedName);
        return new IdToken($this->config()['issuer'], $clientId, $idTokenUser);
    }

    public function generateAccessToken(string $clientId, string $sub): AccessToken
    {
        return new AccessToken($this->config()['issuer'], $clientId, $sub);
    }

    public function generateRefreshToken(string $clientId, string $sub)
    {
        return new RefreshToken($clientId);
    }


    protected function config(): array
    {
        return config('auth');

    }
}

<?php

namespace App\Auth;

use App\Auth\User\UserService;
use App\models\AccessToken;
use App\models\IdToken;
use App\models\IdTokenUser;
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


    public function generateIdToken(string $clientId, string $distinguishedName): string
    {
        $idTokenUser = $this->userService->getUserData($distinguishedName);
        $idToken = new IdToken($this->config()['issuer'], $clientId, $idTokenUser);
        return $idToken->generateJWT();
    }

    public function generateAccessToken(string $clientId, string $sub): string
    {
        $config = $this->config();
        Log::debug(print_r($config, true));
        $accessToken = new AccessToken($this->config()['issuer'], $clientId, $sub);
        return $accessToken->generateJWT();
    }


    protected function config(): array
    {
        return config('auth');

    }
}

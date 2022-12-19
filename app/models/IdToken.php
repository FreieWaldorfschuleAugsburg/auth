<?php

namespace App\models;

use App\models\interfaces\Token;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class IdToken extends IdTokenUser implements Token
{
    public string $iss;
    public string $aud;
    public string $iat;
    public string $exp;

    public function __construct(string $iss, string $aud, IdTokenUser $idTokenUser)
    {
        $this->iss = $iss;
        $this->aud = $aud;
        $this->iat = Carbon::now()->timestamp;
        $this->exp = Carbon::now()->addHour()->timestamp;
        parent::__construct($idTokenUser->sub, $idTokenUser->preferred_username, $idTokenUser->given_name, $idTokenUser->family_name, $idTokenUser->email, $idTokenUser->groups);

    }

    public function getJWT(): string
    {
        $algorithm = $this->config()['token_algorithm'];
        $privateKey = file_get_contents(app()->basePath($this->config()['private_key_path']));
        $token = JWT::encode((array)$this, $privateKey, $algorithm);
        return $token;
    }


    public function config()
    {
        return config('auth');
    }


}

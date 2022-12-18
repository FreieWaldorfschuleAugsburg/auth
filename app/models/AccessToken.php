<?php

namespace App\models;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class AccessToken
{
    public string $iss;
    public string $aud;
    public string $iat;
    public string $exp;

    public string $sub;

    public function __construct(string $iss, string $aud, string $sub)
    {
        $this->iss = $iss;
        $this->aud = $aud;
        $this->iat = Carbon::now()->timestamp;
        $this->exp = Carbon::now()->addHour()->timestamp;
        $this->sub = $sub;

    }

    public function generateJWT(): string
    {
        $algorithm = $this->config()['token_algorithm'];
        $privateKey = file_get_contents(app()->basePath($this->config()['private_key_path']));
        return JWT::encode((array)$this, $privateKey, $algorithm);
    }


    protected function config()
    {
        return config('auth');
    }


}

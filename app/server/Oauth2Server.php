<?php

namespace App\server;

use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\models\AuthClient;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class Oauth2Server
{
    /**
     * @throws InvalidParameterException
     */


    public static function denyAuthorizationRequest(string $callbackUrl): \Symfony\Component\HttpFoundation\Response
    {

        return Inertia::location("$callbackUrl/callback?authorization=denied");

    }




    /**
     * @throws InvalidClientException
     */

    /**
     * @throws InvalidClientException
     */


    public static function generateRefreshToken(): string
    {
        $key = env('AUTH_KEY');
        $payload = [
            'iss' => env('AUTH_ISSUER'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDays(30)->timestamp];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return Crypt::encrypt($jwt);
    }


}





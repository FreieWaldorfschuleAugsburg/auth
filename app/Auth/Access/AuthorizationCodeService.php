<?php

namespace App\Auth\Access;

use App\models\AuthCode;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthorizationCodeService
{
    public function generateAuthorizationCode(string $clientId, string $redirectUri, string $samaccountname, string $codeIdentifier): string
    {
        Log::debug("Code requested");
        $key = env('AUTH_KEY');
        $payload = $this->generateAuthorizationCodePayload($clientId, $redirectUri, $samaccountname, $codeIdentifier);
        $token = JWT::encode($payload, $key, 'HS256');
        Log::debug("Authorization Code granted");
        return trim(Crypt::encrypt($token));
    }

    public function storeAuthorizationCode(string $codeId, string $authorizationCode)
    {
        $storableAuthCode = new AuthCode();
        $storableAuthCode->code_id = $codeId;
        $storableAuthCode->code_hash = Hash::make($authorizationCode);
        $storableAuthCode->save();

    }


    function generateAuthorizationCodePayload(string $clientId, string $redirectUri, string $samaccountname, string $codeId): array
    {
        return [
            'iss' => env('AUTH_ISSUER'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(2)->timestamp,
            'sub' => $samaccountname,
            'client_id' => $clientId,
            'code_identifier' => $codeId,
            'redirect_uri' => $redirectUri
        ];


    }


}

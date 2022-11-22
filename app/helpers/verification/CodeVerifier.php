<?php

namespace App\helpers\verification;

use App\Models\oAuthClient;
use App\Models\StoredAuthenticationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CodeVerifier
{


    public static function verifyExp($decoded): bool
    {
        return Carbon::now()->timestamp <= $decoded->exp;
    }

    public static function verifyRedirectionUri(oAuthClient $client, $decoded): bool
    {
        return $client->redirect === $decoded->redirect_uri;
    }

    public static function verifyCodeHash(StoredAuthenticationCode $code, $decoded): bool
    {

        return $code->hashed_code === Hash::make($decoded);

    }

}

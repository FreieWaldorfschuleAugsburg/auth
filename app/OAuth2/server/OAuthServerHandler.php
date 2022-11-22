<?php

namespace App\OAuth2\server;

use App\Exceptions\InvalidAuthenticationCode;
use App\Models\AuthorizationCode;
use Illuminate\Http\Request;

class OAuthServerHandler
{

    /**
     * @throws InvalidAuthenticationCode
     */
    public static function handleTokenIssueWithAuthorizationCode(Request $request, $parameters)
    {
        $code = $parameters['code'];
        $decoded = AuthorizationCode::verifyCode(AuthorizationCode::decrypt($code));
        // go on from here

    }

    public static function handleTokenIssueWithPassword(Request $request, $parameters)
    {

    }


}

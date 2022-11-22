<?php

namespace App\Http\Controllers;


use App\Exceptions\InvalidAuthorizationRequest;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\Helpers\Authentication\ClientHelper;
use App\helpers\authentication\FormHelper;
use App\Ldap\LdapUserWithTokens;
use App\Models\AuthorizationCode;
use App\Models\StoredAuthenticationCode;
use App\OAuth2\server\OAuthServerHandler;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Crypto\Rsa\Exceptions\FileDoesNotExist;

class OAuthenticationController
{
    /**
     * @throws InvalidClientException
     * @throws InvalidParameterException
     */
    public function authorize(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Routing\Redirector
    {
        $params = FormHelper::parseAuthorizationRequestParameters($request);
        $clientHelper = new ClientHelper();
        if ($clientHelper->verifyClient($params['client_id'], $params['redirect_uri'])) {
            foreach ($params as $key => $param) {
                session()->put($key, $param);
            }
            if (!Auth::user()) {
                return redirect('/auth/login');
            } else return redirect('/auth/confirm');
        }
        throw new InvalidClientException('client verification failed');
    }


    public function viewLogin()
    {
        Log::error("Logging in view");
        return Inertia::render('Login');
    }

    /**
     */
    public function storeLogin(Request $request): \Symfony\Component\HttpFoundation\Response

    {
        $credentials = $request->validate([
            'samaccountname' => 'required',
            'password' => 'required'
        ]);
        $passed = Auth::attempt($credentials);
        if (!$passed) {
            session()->regenerate(true);
            return Inertia::location('/auth/login');
        }
        return Inertia::location('/auth/confirm');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function viewConfirm()
    {
        return Inertia::render('Confirm', [
            'client_name' => ClientHelper::getClient(session()->get('client_id')),
            'client_id' => Crypt::encryptString(session()->get('client_id')),
            'redirect_uri' => Crypt::encryptString(session()->get('redirect_uri')),
            'response_type' => Crypt::encryptString(session()->get('response_type')),
            'state' => Crypt::encryptString(session()->get('state'))]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidAuthorizationRequest
     * @throws FileDoesNotExist
     */
    public function acceptAuthorization(Request $request)
    {
        $verifiedParams = FormHelper::verifyAuthorizationGrantRequest($request);
        $user = Auth::user();
        $authorizationCode = new AuthorizationCode($verifiedParams['client_id'], $user['samaccountname'][0], Carbon::now()->addMinutes(2), $verifiedParams['redirect_uri']);
        $token = $authorizationCode->getJWT();
        $storedAuthCode = $authorizationCode->getDataBaseVersion($token);
        $storedAuthCode->save();
        session()->invalidate();

        return Inertia::location("$verifiedParams[redirect_uri]?code=$token&state=$verifiedParams[state]");
    }

    public function denyAuthorization()
    {
        $redirectUri = session()->get('redirect_uri');
        session()->regenerate(true);
        return Inertia::location($redirectUri);
    }

    public function IssueAccessToken(Request $request)
    {
        $verifiedParams = FormHelper::parseAccessTokenRequestFormData($request);
        if ($verifiedParams['grant_type'] === 'code') {
            OAuthServerHandler::handleTokenIssueWithAuthorizationCode($request, $verifiedParams);
        }
        if ($verifiedParams['grant_type'] === 'password') {
            OAuthServerHandler::handleTokenIssueWithPassword($request, $verifiedParams);
        }


    }

}

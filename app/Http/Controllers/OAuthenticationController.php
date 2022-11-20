<?php

namespace App\Http\Controllers;


use App\Exceptions\InvalidAuthorizationRequest;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\Helper\Authentication\ClientHelper;
use App\helpers\authentication\FormHelper;
use App\Models\AuthorizationCode;
use Carbon\Carbon;
use Carbon\Traits\Date;
use http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OAuthenticationController
{
    /**
     * @throws InvalidClientException
     * @throws InvalidParameterException
     */
    public function authorize(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Routing\Redirector
    {
        $params = FormHelper::parseParameters($request);
        $clientHelper = new ClientHelper();
        $client = $clientHelper->verifyClient($params['client_id'], $params['redirect_uri']);
        foreach ($params as $key => $param) {
            session()->put($key, $param);
        }
        if (!Auth::user()) {
            return redirect('/auth/login');
        } else return redirect('/auth/confirm');
    }


    public function viewLogin()
    {
        return Inertia::render('login');
    }

    /**
     * @throws InvalidAuthorizationRequest
     */
    public function storeLogin(Request $request)
    {
        $credentials = $request->validate([
            'samaccountname' => 'required',
            'password' => 'required'
        ]);
        if (!$credentials || !Auth::check()) {
            session()->regenerate(true);
            return redirect('/auth/login');
        }
        return redirect('/');
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
     */
    public function acceptAuthorization(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $verifiedParams = FormHelper::verifyClientData($request);
        $authorizationCode = new AuthorizationCode();
        $authorizationCode->setClientId($verifiedParams['client_id']);
        $authorizationCode->setUserId(Auth::id());
        $authorizationCode->setExpirationDate(Carbon::now()->addMinutes(2));
        $authorizationCode->setRedirectUri($verifiedParams['redirect_uri']);
        $encryptedCode = Crypt::encryptString(json_encode($authorizationCode));
        return Inertia::location("$verifiedParams[redirect_uri]?code=$encryptedCode&state=$verifiedParams[state]");
    }

    public function denyAuthorization()
    {

    }

    public function IssueAccessToken(Request $request)
    {


    }

}

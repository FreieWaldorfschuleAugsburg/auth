<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidAuthorizationRequest;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\models\AuthClient;
use App\server\Oauth2Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Controller;
use Inertia\Inertia;

class OAuth2Controller extends Controller
{

    /**
     * @throws InvalidParameterException
     * @throws InvalidClientException
     */
    public function authorize(Request $request): bool|\Inertia\Response
    {
        $requestParameters = [
            'client_id' => $request->input('client_id'),
            'redirect_uri' => $request->input('redirect_uri'),
            'scope' => $request->input('scope'),
            'response_type' => $request->input('response_type'),
            'state' => $request->input('state')
        ];

        $clientParametersToVerify = [
            'client_id' => $requestParameters['client_id'],
            'redirect_uri' => $requestParameters['redirect_uri']
        ];

        Session::start();
        Session::put('requestParameters', $requestParameters);
        if (Oauth2Server::verifyRequestParameters($requestParameters) && AuthClient::verifyClientAttributes($requestParameters['client_id'], $clientParametersToVerify)) {
            if (Auth::user()) {
                return Inertia::render('Confirm', Oauth2Server::getParameterPropsForConfirmDialogue($requestParameters));
            } else return Inertia::render('Login');
        }
        return false;
    }

    /**
     * @throws InvalidParameterException
     */
    public function grantAuthorization(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $providedParameters = [
            'client_id' => $request->input('client_id'),
            'redirect_uri' => $request->input('redirect_uri'),
            'scope' => $request->input('scope'),
            'response_type' => $request->input('response_type'),
            'state' => $request->input('state')];

        $previouslyProvidedParameters = Session::get('requestParameters');
        foreach ($previouslyProvidedParameters as $previouslyProvidedParameterKey => $previouslyProvidedParameterValue) {
            if (!$providedParameters[$previouslyProvidedParameterKey] !== $previouslyProvidedParameterValue) {
                throw new InvalidParameterException('Parameters do not match!');
            }
        }
        $user = Auth::user();
        $userName = $user->getName();
        $authorizationCode = Oauth2Server::generateAuthorizationCode($previouslyProvidedParameters['client_id'], $providedParameters['redirect_uri'], $userName);
        Session::put('authorizationCode', $authorizationCode);
        return Oauth2Server::returnToCallbackUrlWithAuthorizationCode($previouslyProvidedParameters['redirect_uri'], $authorizationCode);
    }

    public function denyAuthorization(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return Oauth2Server::denyAuthorizationRequest($request->input('redirect_uri'));
    }


    /**
     * @throws InvalidParameterException
     * @throws InvalidClientException
     */
    public function login(Request $request): \Inertia\Response|\Symfony\Component\HttpFoundation\Response
    {
        $credentials = [
            'samaccountname' => $request->input('samaccountname'),
            'password' => $request->input('password')
        ];
        if (!Auth::attempt($credentials)) {
            return Inertia::location('login', [
            ]);
        }
        $previouslyProvidedParameters = Session::get('requestParameters');

        if (!$previouslyProvidedParameters) {
            throw new InvalidParameterException('No parameters provided');
        }


        return Inertia::render('Confirm', Oauth2Server::getParameterPropsForConfirmDialogue($previouslyProvidedParameters));
    }


    /**
     * @throws InvalidAuthorizationRequest
     */
    public function token(Request $request)
    {
        $tokenRequestData = [
            'grant_type' => $request->input('grant_type'),
            '$authorization_code' => $request->input('code'),
            'client_id' => $request->input('client_id'),
            'client_secret' => $request->input('client_secret'),
            'redirect_uri' => $request->input('redirect_uri')
        ];

        if (!Oauth2Server::verifyTokenRequest($tokenRequestData)) {
            throw new InvalidAuthorizationRequest('Provided data does not match');
        }

    }


}

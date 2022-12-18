<?php

namespace App\Http\Controllers;

use App\Auth\Access\AuthorizationCodeService;
use App\Auth\TokenService;
use App\Exceptions\InvalidAuthenticationCode;
use App\Exceptions\InvalidAuthorizationRequest;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\Http\ParameterService;
use App\models\AuthClient;
use App\models\AuthCode;
use App\models\DecodedAuthorizationCode;
use App\server\Oauth2Server;
use App\server\ParameterHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Controller;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;

class OAuth2Controller extends Controller
{
    protected TokenService $tokenService;
    protected ParameterService $parameterService;

    protected AuthorizationCodeService $authorizationCodeService;

    /**
     * @param TokenService $tokenService
     * @param ParameterService $parameterService
     */
    public function __construct(TokenService $tokenService, ParameterService $parameterService, AuthorizationCodeService $authorizationCodeService)
    {
        $this->tokenService = $tokenService;
        $this->parameterService = $parameterService;
        $this->authorizationCodeService = $authorizationCodeService;
    }


    /**
     * @throws InvalidParameterException
     * @throws InvalidClientException
     */
    public function authorize(Request $request): bool|\Inertia\Response
    {
        $requestParameters = $this->parameterService->getRequestParameters($request);

        $clientParametersToVerify = [
            'client_id' => $requestParameters['client_id'],
            'redirect_uri' => $requestParameters['redirect_uri']
        ];
        Session::start();
        Session::put('requestParameters', $requestParameters);
        Log::debug("Session parameters set");
        Session::save();

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
        Log::debug("Authorization Grant requested");
        $previouslyProvidedParameters = Session::get('requestParameters');
        $requestParameters = $this->parameterService->getRequestParameters($request);
        if (!ParameterHelper::validateParameters($previouslyProvidedParameters, $requestParameters)) {
            Oauth2Server::denyAuthorizationRequest($requestParameters['redirect_uri']);
        }
        $distinguishedName = Auth::user()->getDn();
        $codeIdentifier = Uuid::uuid4();
        $authorizationCode = $this->authorizationCodeService->generateAuthorizationCode($requestParameters['client_id'], $requestParameters['redirect_uri'], $distinguishedName, $codeIdentifier);
        $this->authorizationCodeService->storeAuthorizationCode($codeIdentifier, $authorizationCode);
        return Oauth2Server::returnToCallbackUrlWithAuthorizationCode($previouslyProvidedParameters['redirect_uri'], $authorizationCode, $previouslyProvidedParameters['state']);
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
     * @throws InvalidAuthenticationCode
     */
    public function token(Request $request)
    {
        Log::debug("Token requested");
        $tokenRequestData = [
            'grant_type' => $request->input('grant_type'),
            'authorization_code' => $request->input('code'),
            'redirect_uri' => $request->input('redirect_uri')
        ];
        Log::debug('app.requests', ['request' => $request]);
        if (!AuthCode::verifyAuthenticationCode($tokenRequestData['authorization_code'])) {
            throw new InvalidAuthenticationCode('Provided code does not match issued code');
        }
        $decodedAuthorizationCode = AuthCode::decodeAuthenticationCode($tokenRequestData['authorization_code']);

        $accessToken = $this->tokenService->generateAccessToken($decodedAuthorizationCode->client_id, $decodedAuthorizationCode->sub);
        $idToken = $this->tokenService->generateIdToken($decodedAuthorizationCode->client_id, $decodedAuthorizationCode->sub);
        $refreshToken = Oauth2Server::generateRefreshToken();
        Log::debug("Returning token...");
        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'id_token' => $idToken,
            'refresh_token' => $refreshToken
        ]);
    }

    /**
     * @throws InvalidParameterException
     */


}

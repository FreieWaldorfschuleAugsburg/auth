<?php

namespace App\Http\Controllers;

use App\Auth\Access\AuthorizationCodeService;
use App\Auth\TokenService;
use App\Exceptions\InvalidAuthenticationCode;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\Http\ParameterService;
use App\Http\Requests\LoginRequest;
use App\models\AuthClient;
use App\models\AuthCode;
use App\models\RefreshToken;
use App\models\TokenResponse;
use App\server\Oauth2Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $clientAttributesToVerify = [
            'client_id' => $requestParameters['client_id'],
            'redirect_uri' => $requestParameters['redirect_uri']
        ];
        if ($this->parameterService->verifyRequestParameters($requestParameters) && AuthClient::verifyClientAttributes($requestParameters['client_id'], $clientAttributesToVerify)) {
            Session::put("requestParameters", $requestParameters);
            if (Auth::user()) {
                return Inertia::render('Confirm', $this->parameterService->getParametersForConfirm($requestParameters));
            } else return Inertia::render('Login');
        }
        return false;
    }

    /**
     * @throws InvalidParameterException
     */
    public function grantAuthorization(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $storedRequestParameters = Session::get('requestParameters');
        Log::debug("Session values");
        Log::debug($storedRequestParameters);
        $requestParameters = $this->parameterService->getRequestParameters($request);
        Log::debug("New values");
        Log::debug($requestParameters);
        if ($this->parameterService->compareParameterArray($storedRequestParameters, $requestParameters)) {
            Oauth2Server::denyAuthorizationRequest($requestParameters['redirect_uri']);
        }
        $distinguishedName = Auth::user()->getDn();
        $codeIdentifier = Uuid::uuid4();
        $authorizationCode = $this->authorizationCodeService->generateAuthorizationCode($requestParameters['client_id'], $requestParameters['redirect_uri'], $distinguishedName, $codeIdentifier);
        $this->authorizationCodeService->storeAuthorizationCode($codeIdentifier, $authorizationCode);
        return $this->redirectToCallback($storedRequestParameters['redirect_uri'], $authorizationCode, $storedRequestParameters['state']);
    }

    public function denyAuthorization(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return redirect()->back();
    }


    /**
     * @throws InvalidParameterException
     * @throws InvalidClientException
     */
    public function login(LoginRequest $request): \Inertia\Response|\Symfony\Component\HttpFoundation\Response
    {

        $request->validated();

        if (request()->has('prevalidate')) {
            return redirect()->back();
        }

        Log::debug("Test");
        $credentials = [
            'samaccountname' => $request->input('samaccountname'),
            'password' => $request->input('password')
        ];
        if (!Auth::attempt($credentials)) {
            return Inertia::location('login', [
            ]);
        }
        $storedRequestParameters = Session::get('requestParameters');
        if (!$storedRequestParameters) {
            throw new InvalidParameterException('No parameters provided: Your session is probably invalid');
        }

        return Inertia::render('Confirm', $this->parameterService->getParametersForConfirm($storedRequestParameters));
    }


    /**
     * @throws InvalidAuthenticationCode
     * @throws InvalidClientException
     */
    public function token(Request $request): \Illuminate\Http\JsonResponse
    {
        $grantType = $request->input('grant_type');
        if ($grantType === 'code' || $grantType === 'authorization_code') {
            $authorizationCode = $request->input('code');
            if (!AuthCode::verifyAuthenticationCode($authorizationCode)) {
                throw new InvalidAuthenticationCode('Provided code does not match issued code');
            }
            $decodedAuthorizationCode = AuthCode::decodeAuthenticationCode($authorizationCode);
            return response()->json($this->getTokenResponse($decodedAuthorizationCode->client_id, $decodedAuthorizationCode->sub));
        } else if ($grantType === 'refresh_token') {
            $refreshToken = $request->input('refresh_token');
            $clientSecret = $request->input('client_secret');
            $verifiedToken = RefreshToken::verify($refreshToken, $clientSecret);
            if ($verifiedToken) {
                $sub = $verifiedToken->sub;
                $clientId = $verifiedToken->client_id;
                return response()->json($this->getTokenResponse($clientId, $sub));
            }
        }
        return response()->json([
            "error" => "invalid_token",
        ], 401);
    }


    function redirectToCallback(string $callbackUrl, string $authorizationCode, string $state): \Symfony\Component\HttpFoundation\Response
    {
        return Inertia::location("$callbackUrl?code=$authorizationCode&state=$state");
    }

    function getTokenResponse(string $client_id, string $sub): array
    {
        $accessToken = $this->tokenService->generateAccessToken($client_id, $sub);
        $idToken = $this->tokenService->generateIdToken($client_id, $sub);
        $refreshToken = $this->tokenService->generateRefreshToken($client_id, $sub);
        $tokenResponse = new TokenResponse($accessToken->getJWT(), $idToken->getJWT(), $refreshToken->getJWT(), 'Bearer');
        return (array)$tokenResponse;
    }


}

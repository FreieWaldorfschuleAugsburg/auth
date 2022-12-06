<?php

namespace App\server;

use App\Exceptions\InvalidAuthenticationCode;
use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\models\AuthClient;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use LdapRecord\Models\ActiveDirectory\User;

class Oauth2Server
{

    public static function generateAuthorizationCode(string $clientId, string $redirectUri, string $samaccountname, string $codeIdentifier): string
    {
        $key = env('AUTH_KEY');
        $payload = [
            'iss' => env('AUTH_ISSUER'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(2)->timestamp,
            'sub' => $samaccountname,
            'client_id' => $clientId,
            'code_identifier' => $codeIdentifier,
            'redirect_uri' => $redirectUri
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        return trim(Crypt::encrypt($token));
    }

    public static function verifyAuthorizationCode(string $authorizationCode): bool
    {
        $key = env('AUTH_KEY');
        $decodedToken = Crypt::decrypt($authorizationCode);
        $decoded = JWT::decode($decodedToken, new Key($key, 'HS256'));
        $decodedArray = (array)$decoded;

        //TODO: Rework verification
        if (!$decodedArray['iss'] === env('AUTH_ISSUER')) {
            return false;
        } else return true;
    }


    /**
     * @throws InvalidParameterException
     */
    public static function verifyRequestParameters(array $requestParameters): bool
    {
        $requiredParameters = config('parameters.authorization_request');
        foreach ($requiredParameters as $requiredParameter) {
            if (!$requestParameters[$requiredParameter]) {
                throw new InvalidParameterException("Please provide parameter $requiredParameter!");
            }
        }
        return true;
    }

    public static function denyAuthorizationRequest(string $callbackUrl): \Symfony\Component\HttpFoundation\Response
    {

        return Inertia::location("$callbackUrl/callback?authorization=denied");

    }


    public static function returnToCallbackUrlWithAuthorizationCode(string $callbackUrl, string $authorizationCode): \Symfony\Component\HttpFoundation\Response
    {
        return Inertia::location("$callbackUrl?code=$authorizationCode&state=test");
    }

    /**
     * @throws InvalidClientException
     */
    public static function getParameterPropsForConfirmDialogue(array $providedRequestParameters): array
    {
        $client = AuthClient::getClient($providedRequestParameters['client_id']);
        if (!$client) {
            throw new InvalidClientException("Client not found");
        }

        Log::alert($client);
        return [
            'client_id' => $client->client_id,
            'client_name' => $client->client_name,
            'redirect_uri' => $providedRequestParameters['redirect_uri'],
            'response_type' => $providedRequestParameters['response_type'],
            'state' => $providedRequestParameters['state']
        ];


    }


    public static function verifyTokenRequest(array $tokenRequestData): bool
    {
        if (!AuthClient::verifyClientData($tokenRequestData)) {
            return false;
        }
        return true;
    }


    public static function generateIdToken(string $username)
    {

        $user = User::find($username);
        $key = env('AUTH_KEY');

        $groups = [];
        foreach ($user->groups()->recursive()->get() as $userGroup) {
            $groups[] = $userGroup->getName();
        }

        $payload = [
            'iss' => env('AUTH_ISSUER'),
            'sub' => $user->samaccountname[0],
            'preferred_username' => $username,
            'groups' => $groups,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHour()->timestamp,
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function generateAccessToken(string $username)
    {
        $key = env('AUTH_KEY');
        $payload = [
            'iss' => env('AUTH_ISSUER'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHour()->timestamp,
            'preferred_username' => $username];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return Crypt::encrypt($jwt);

    }

    public static function generateRefreshToken()
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





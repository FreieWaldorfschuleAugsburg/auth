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


    public static function returnToCallbackUrlWithAuthorizationCode(string $callbackUrl, string $authorizationCode, string $state): \Symfony\Component\HttpFoundation\Response
    {
        return Inertia::location("$callbackUrl?code=$authorizationCode&state=$state");
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
        return [
            'client_id' => $client->client_id,
            'client_name' => $client->client_name,
            'redirect_uri' => $providedRequestParameters['redirect_uri'],
            'scope' => $providedRequestParameters['scope'],
            'response_type' => $providedRequestParameters['response_type'],
            'state' => $providedRequestParameters['state']
        ];
    }

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





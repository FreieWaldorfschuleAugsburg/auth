<?php


namespace App\Helpers\Authentication;

use App\Exceptions\InvalidClientException;
use App\Models\oAuthClient;

class ClientHelper
{

    /**
     * @throws InvalidClientException
     */
    public function verifyClient(string $clientId, string $redirectUrl): bool
    {
        $client = oAuthClient::find($clientId);
        if ($client && $this->verifyRedirectUrl($client, $redirectUrl)) {
            return true;
        } else throw new InvalidClientException('client verification failed');
    }

    /**
     * @throws InvalidClientException
     */
    public function verifyClientIdAndSecret(string $clientId, string $clientSecret, string $redirectUrl)
    {
        $client = oAuthClient::find($clientId);
        if ($client) {
            if ($this->verifyRedirectUrl($client, $redirectUrl) && $this->verifyClientSecret($client, $clientSecret)) {
                return $client;
            }
        }
        throw new InvalidClientException('client verification failed');
    }

    /**
     * @throws InvalidClientException
     */
    function verifyRedirectUrl(oAuthClient $client, string $redirectUrl): bool
    {
        return $client->redirect === $redirectUrl;
    }


    /**
     * @throws InvalidClientException
     */
    function verifyClientSecret(oAuthClient $client, string $clientSecret): bool
    {
        return $client->secret === $clientSecret;
    }


    public static function getClient(string $clientId)
    {
        return oAuthClient::find($clientId);
    }


}

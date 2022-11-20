<?php


namespace App\Helper\Authentication;

use App\Exceptions\InvalidClientException;
use App\Models\oAuthClient;

class ClientHelper
{

    /**
     * @throws InvalidClientException
     */
    public function verifyClient(int $clientId, string $redirectUrl)
    {
        $client = oAuthClient::find($clientId);
        if ($client) {
            $this->verifyRedirectUrl($client, $redirectUrl);
        } else throw new InvalidClientException('client verification failed');
        return $client;
    }
    /**
     * @throws InvalidClientException
     */
    public function verifyClientIdAndSecret(int $clientId, string $clientSecret, string $redirectUrl)
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
    function verifyRedirectUrl(oAuthClient $client, string $redirectUrl)
    {
        if ($client->redirect === $redirectUrl) {
            return $client;
        } else throw new InvalidClientException('client verification failed');
    }


    /**
     * @throws InvalidClientException
     */
    function verifyClientSecret(oAuthClient $client, string $clientSecret)
    {
        if ($client->secret === $clientSecret) {
            return $client;
        } else throw new InvalidClientException('client verification failed');
    }


    public static function getClient(int $clientId)
    {
        return oAuthClient::find($clientId);
    }


}

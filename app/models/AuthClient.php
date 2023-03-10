<?php


namespace App\models;

use App\Exceptions\InvalidClientException;
use Illuminate\Database\Eloquent\Model;

class AuthClient extends Model
{
    protected $table = 'auth_clients';
    protected $primaryKey = 'client_id';
    public $incrementing = false;


    public static function getClient(string $clientId)
    {
        return AuthClient::find($clientId);
    }

    /**
     * @throws InvalidClientException
     */
    public static function verifyClientData(array $tokenRequestData): bool
    {
        $client = self::getClient($tokenRequestData['client_id']);
        if (!$client) {
            throw new InvalidClientException("Client not found");
        }

        if ($client->client_secret !== $tokenRequestData['client_secret'] || $client->redirect_uri !== $tokenRequestData['redirect_uri']) {
            return false;
        } else return true;
    }

    /**
     * @throws InvalidClientException
     */
    public static function verifyClientAttributes(string $clientId, array $attributesToVerify): bool
    {

        $allowedAttributes = config('client.attributes');
        $client = self::getClient($clientId);
        if (!$client) {
            throw new InvalidClientException('Client does not exist');
        }
        if ($client->revoked === true) {
            throw new InvalidClientException('Client has been revoked!');
        }
        foreach ($attributesToVerify as $attribute => $value) {
            if (!$client->$attribute === $value || !in_array($attribute, $allowedAttributes)) {
                throw new InvalidClientException("$attribute did not match provided value: $client->$attribute !== $value");
            }
        }
        return true;
    }


    /**
     * @throws InvalidClientException
     */
    public static function verifyClientSecret(string $clientId, string $clientSecret)
    {
        $client = self::getClient($clientId);
        if (!$client) {
            throw new InvalidClientException('Client does not exist');
        }

        if ($client->client_secret === $clientSecret) {
            return true;
        }
        return false;
    }


}

<?php

namespace App\Models;


use App\Exceptions\InvalidAuthenticationCode;
use App\Helpers\Authentication\ClientHelper;
use App\helpers\verification\CodeVerifier;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use Ramsey\Uuid\Uuid;
use Spatie\Crypto\Rsa\Exceptions\FileDoesNotExist;
use Spatie\Crypto\Rsa\PrivateKey;

class AuthorizationCode
{
    public string $id;
    public string $clientId;
    public string $userName;
    public Carbon $expirationDate;
    public string $redirectUri;

    /**
     * @param string $clientId
     * @param string $userName
     * @param Carbon $expirationDate
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $userName, Carbon $expirationDate, string $redirectUri)
    {
        $this->id = Uuid::uuid4();
        $this->clientId = $clientId;
        $this->userName = $userName;
        $this->expirationDate = $expirationDate;
        $this->redirectUri = $redirectUri;
    }


    /**
     * @throws FileDoesNotExist
     */
    public function getJWT()
    {
        $client = oAuthClient::find($this->clientId);
        $key = getenv('AUTH_KEY');
        $payload = [
            'iss' => getenv('AUTH_ISSUER'),
            'id' => $this->id,
            'exp' => $this->expirationDate->timestamp,
            'iat' => time(),
            'client_id' => $this->clientId,
            'preferred_username' => $this->userName,
            'redirect_uri' => $this->redirectUri
        ];
        return Crypt::encrypt(JWT::encode($payload, $key, 'HS256'));
    }

    public function getDataBaseVersion(string $token): StoredAuthenticationCode
    {

        return StoredAuthenticationCode::create([
            'id' => $this->id,
            'samaccountname' => $this->userName,
            'client_id' => $this->clientId,
            'revoked' => false,
            'hashed_code' => Hash::make($token),
            'expires_at' => $this->expirationDate
        ]);

    }

    public static function decrypt(string $code): string
    {
        return Crypt::decrypt($code);
    }

    /**
     * @throws InvalidAuthenticationCode
     */
    public static function verifyCode(string $decryptedCode)
    {
        $decoded = JWT::decode($decryptedCode, getenv('AUTH_KEY'));
        $client = ClientHelper::getClient($decoded->client_id);
        $authCode = StoredAuthenticationCode::find($decoded->id);
        if ($client && $authCode) {
            if (CodeVerifier::verifyExp($decoded) && CodeVerifier::verifyRedirectionUri($client, $decoded) && CodeVerifier::verifyCodeHash($authCode, $decoded)) {
                return $decoded;
            }
        }
        throw new InvalidAuthenticationCode('code invalid');
    }


    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return Carbon
     */
    public function getExpirationDate(): Carbon
    {
        return $this->expirationDate;
    }

    /**
     * @param Carbon $expirationDate
     */
    public function setExpirationDate(Carbon $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }


}

<?php

namespace App\models;

use App\Exceptions\InvalidClientException;
use App\models\interfaces\EncryptedToken;
use App\models\interfaces\Token;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use LdapRecord\LdapRecordException;
use Ramsey\Uuid\Uuid;

class RefreshToken extends Model implements EncryptedToken
{

    public string $token_id;

    public string $client_id;

    public string $sub;
    public float $expires;


    protected $table = 'refresh_tokens';
    protected $primaryKey = 'client_id';
    protected $fillable = ['token_id', 'client_id', 'token_hash', 'expires'];

    public function __construct(string $client_id, string $sub, array $attributes = [])
    {
        $this->token_id = Uuid::uuid4();
        $this->expires = Carbon::now()->addDays(90)->timestamp;
        $this->client_id = $client_id;
        $this->sub = $sub;
        parent::__construct($attributes);
    }

    public function store()
    {
        $this->token_hash = Hash::make($this);
        $this->save();
    }


    public function getJWT(): string
    {
        $algorithm = $this->config()['token_algorithm'];
        $privateKey = file_get_contents(app()->basePath($this->config()['private_key_path']));
        return JWT::encode((array)$this, $privateKey, $algorithm);

    }

    public function encode(string $tokenToEncode): string
    {
        return Crypt::encrypt($tokenToEncode);
    }

    public static function config()
    {
        return config('auth');
    }

    public static function decode(string $encodedTokenToDecode): RefreshToken
    {
        $algorithm = RefreshToken::config()['token_algorithm'];
        $publicKey = file_get_contents(app()->basePathRefreshToken::config()['public_key_path']);
        $decodedToken = Crypt::decrypt($encodedTokenToDecode);
        $decodedArray = (array)JWT::decode($decodedToken, new Key($publicKey, $algorithm));
        return new RefreshToken($decodedArray['client_id'], $decodedArray['expires'], $decodedToken);
    }


    /**
     * @throws InvalidClientException
     */
    public static function verify(string $encodedRefreshToken, string $clientSecret): RefreshToken|bool
    {
        $decodedRefreshToken = RefreshToken::decode($encodedRefreshToken);

        if (AuthClient::verifyClientSecret($decodedRefreshToken->client_id, $clientSecret)) {
            return $decodedRefreshToken;
        }

        return false;
    }


}

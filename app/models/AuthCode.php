<?php


namespace App\models;

use App\Exceptions\InvalidAuthenticationCode;
use App\Exceptions\InvalidClientException;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthCode extends Model
{
    protected $table = 'auth_codes';
    protected $primaryKey = 'code_id';
    public $timestamps = true;
    public $fillable = ['code_id', 'code_identifier', 'code_hash'];

    /**
     * @throws InvalidAuthenticationCode
     */
    public static function verifyAuthenticationCode(string $providedAuthenticationCode): bool
    {
        $decoded = self::decodeAuthenticationCode($providedAuthenticationCode);
        //check if code is not expired
        if ($decoded->exp <= Carbon::now()->timestamp) {
            throw new InvalidAuthenticationCode('Code has expired!');
        }
        //check for client validation
        $client = AuthClient::getClient($decoded->client_id);
        if (!$client || $client->client_id !== $decoded->client_id || $client->redirect_uri !== $decoded->redirect_uri) {
            throw new InvalidAuthenticationCode('The provided client data does not match the issued code');
        }
        //check if code matches hash
        $code = AuthCode::getById($decoded->code_identifier);
        if (!Hash::check($providedAuthenticationCode, $code->code_hash)) {
            $codeHash = Hash::make($providedAuthenticationCode);
            throw new InvalidAuthenticationCode("The provided Code does not matched the issued code because $codeHash !== $code->code_hash");
        }
        return true;

    }


    public static function decodeAuthenticationCode(string $providedAuthenticationCode): DecodedAuthorizationCode
    {
        $key = env('AUTH_KEY');
        $decodedToken = Crypt::decrypt($providedAuthenticationCode);
        $decodedData = JWT::decode($decodedToken, new Key($key, 'HS256'));
        return new DecodedAuthorizationCode((array)$decodedData);

    }


    public static function getById(string $codeIdentifier)
    {
        return AuthCode::find($codeIdentifier);


    }


}

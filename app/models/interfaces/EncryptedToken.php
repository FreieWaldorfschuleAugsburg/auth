<?php

namespace App\models\interfaces;

interface EncryptedToken extends Token
{
    public function encode(string $tokenToEncode);

    public static  function decode(string $encodedTokenToDecode);


}

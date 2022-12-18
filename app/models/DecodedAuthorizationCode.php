<?php

namespace App\models;

class DecodedAuthorizationCode
{
    public string $iss;
    public string $iat;
    public string $exp;
    public string $sub;
    public string $client_id;
    public string $code_identifier;
    public string $redirect_uri;

    /**
     * @param array $decodedCodeArray
     *
     */
    public function __construct(array $decodedCodeArray)
    {
        foreach ($decodedCodeArray as $key => $value) {
            $this->$key = $value;
        }
    }


}






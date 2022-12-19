<?php

namespace App\models;

class TokenResponse
{
    public string $access_token;
    public string $id_token;
    public string $refresh_token;

    public string $token_type;

    /**
     * @param string $access_token
     * @param string $id_token
     * @param string $refresh_token
     * @param string $token_type
     */
    public function __construct(string $access_token, string $id_token, string $refresh_token, string $token_type = 'Bearer')
    {
        $this->access_token = $access_token;
        $this->id_token = $id_token;
        $this->refresh_token = $refresh_token;
        $this->token_type = $token_type;
    }


}

<?php

namespace App\Models\Types;

class TokenRequest
{
    public string $grant_type;
    public int $client_id;
    public string $client_secret;
    public string $redirect_uri;


}

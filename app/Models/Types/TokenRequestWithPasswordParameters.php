<?php

namespace App\Models\Types;

class TokenRequestWithPasswordParameters extends TokenRequest
{
    public string $username;
    public string $password;

}

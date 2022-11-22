<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class StoredAuthenticationCode extends Model
{

    protected $table = 'auth_codes';
    protected $primaryKey = 'id';
    protected $connection = 'sqlite';
    protected $fillable = ['id', 'samaccountname', 'client_id', 'revoked', 'hashed_code', 'expires_at'];


}


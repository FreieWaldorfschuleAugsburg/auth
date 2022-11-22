<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class oAuthClient extends Model
{
    protected $table = 'auth_clients';
    protected $primaryKey = 'id';
    protected $connection = 'sqlite';

}

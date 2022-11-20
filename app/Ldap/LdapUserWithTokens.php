<?php

namespace App\Ldap;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use LdapRecord\Models\Concerns\CanAuthenticate;
use LdapRecord\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class LdapUserWithTokens extends Model implements Authenticatable

{
    use HasApiTokens;
    use CanAuthenticate;

    public function findForPassport($username): ?\LdapRecord\Models\Model
    {
        return $this->where('samaccountname', $username)->first();
    }


    public static function findAndValidateForPassport($username, $password)
    {
        if (Auth::attempt(['samaccountname' => $username, 'password' => $password])) {
            return Auth::user();
        }
    }

    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */


    public static $objectClasses = [];
}

<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use LdapRecord\Laravel\Auth\ListensForLdapBindFailure;

class AuthController extends Controller
{

    use ListensForLdapBindFailure;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'samaccountname' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response("invalid credentials");
        }
        $user = $request->user();
        $true = get_class($user);
        $tokenResult = $user->createToken("Personal Access Token");
        return response(['token' => $tokenResult]);

    }


    public function view()
    {

        return Inertia::render('Login');


    }


    public function __construct()
    {
        $this->listenForLdapBindFailure();
    }

}

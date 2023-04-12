<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\PublicLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return redirect('/home');
        }
        return Inertia::render("Public/Index");
    }

    public function showLogin(): \Inertia\Response
    {
        return Inertia::render("Public/Login");
    }

    public function handleLogin(PublicLoginRequest $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $validated = $request->validated();
        $credentials = [
            "samaccountname" => $validated["username"],
            "password" => $validated["password"]
        ];
        if (!Auth::attempt($credentials)) {
            back()->withErrors(['auth' => 'login.errors.failed']);
        }
        return redirect('/home');
    }

    public function home(): \Inertia\Response
    {
        return Inertia::render('Public/Home');
    }


}

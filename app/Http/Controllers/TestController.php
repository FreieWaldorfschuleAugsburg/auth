<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Controller;
use Inertia\Inertia;

class TestController extends Controller
{
    public function testLogin()
    {
        return Inertia::render('Login');
    }

    public function testConfirm()
    {
        return Inertia::render('Confirm', [
            'client_name' => 'BookStack',
            'scope' => 'Email Profile Name'
        ]);
    }

}

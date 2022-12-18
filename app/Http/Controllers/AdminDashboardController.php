<?php

namespace App\Http\Controllers;

use Inertia\Controller;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {

        return Inertia::render('Dashboard',);


    }


}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has("locale")) {
            $locale = Session::get("locale", Config::get("app.locale"));
        } else {
            $locale = substr($request->server("HTTP_ACCEPT_LANGUAGE"), 0, 2);
        }
        App::setLocale($locale);
        return $next($request);
    }
}

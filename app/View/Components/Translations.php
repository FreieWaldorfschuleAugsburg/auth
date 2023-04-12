<?php

namespace App\View\Components;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Translations extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if (Session::has("locale")) {
            $locale = Session::get("locale", Config::get("app.locale"));
        } else {
            $locale = substr(Request::server("HTTP_ACCEPT_LANGUAGE"), 0, 2);
        }
        App::setLocale($locale);
        $jsonTranslations = [];
        if (File::exists(resource_path("lang/$locale.json"))) {
            $jsonTranslations = json_decode(File::get(resource_path("lang/$locale.json")), true);
        }
        return view('components.translations', ['translations' => array_merge($jsonTranslations)]);
    }
}

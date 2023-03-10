<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use function App\translations;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::share([
            'locale' => function () {
                return app()->getLocale();
            },
//            'language' => function () {
//                return translations(
//                    resource_path('lang/' . app()->getLocale() . 'json')
//                );
//            }
        ]);
    }
}

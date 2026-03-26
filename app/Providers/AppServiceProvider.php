<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->has('lang') && in_array(request('lang'), ['ar', 'en'])) {
            Session::put('locale', request('lang'));
        }

        App::setLocale(Session::get('locale', config('app.locale')));
    }
}
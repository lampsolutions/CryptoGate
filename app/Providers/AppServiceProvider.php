<?php

namespace App\Providers;

use App\Lib\CryptoGateBranding;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $branding = new CryptoGateBranding();
        \View::share('branding', $branding->getConfig());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}

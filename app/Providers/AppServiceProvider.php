<?php

namespace App\Providers;

use App\Lib\CryptoGateBranding;
use App\Lib\CryptoGateFiat;
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

        $branding = new CryptoGateFiat();
        \View::share('global_fiat_currency', $branding->getFiatCurrency());
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

<?php

namespace App\Providers;

use App\Api\Electrum;
use Graze\GuzzleHttp\JsonRpc\Client;
use Illuminate\Support\ServiceProvider;

class ElectrumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('Electrum', function ($app) {
            $config = [
                'BTC' =>  env('ELECTRUM_RPC_ENDPOINT_BTC',  'http://user:'.env('ELECTRUM_RPC_PASSWORD').'@localhost:7771'),
                'DASH' => env('ELECTRUM_RPC_ENDPOINT_DASH', 'http://user:'.env('ELECTRUM_RPC_PASSWORD').'@localhost:7772'),
                'LTC' =>  env('ELECTRUM_RPC_ENDPOINT_LTC',  'http://user:'.env('ELECTRUM_RPC_PASSWORD').'@localhost:7773'),
                'BCH' =>  env('ELECTRUM_RPC_ENDPOINT_BCH',  'http://user:'.env('ELECTRUM_RPC_PASSWORD').'@localhost:7774'),
            ];
            return new Electrum($config);
        });
    }
}

<?php

namespace App\Api;

use Graze\GuzzleHttp\JsonRpc\Client;

class Electrum {

    protected $config;
    protected $clients = [];

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * @param $currency
     * @return Client
     */
    public function client($currency) {
        $currency = strtoupper($currency);

        if(isset($this->clients[$currency])) return $this->clients[$currency];
        $this->clients[$currency] = Client::factory($this->config[$currency]);
        return $this->clients[$currency];
    }

    public function isCurrencyEnabled($currency) {
        return env(strtoupper($currency).'_PUBKEY');
    }

    public function getEnabledCurrencies() {
        $currencies = [];
        foreach(['BTC', 'LTC', 'DASH', 'BCH'] as $c) {
            if(env($c.'_PUBKEY')) $currencies[] = $c;
        }
        return $currencies;
    }
}
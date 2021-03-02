<?php

namespace App\Lib;


class CryptoGateFiat {

    public function __construct() {

    }

    public function getFiatCurrency() {
        // Build Branding Setup
        $config = (array) json_decode(\Cache::get('currency'), true);

        if(!empty($config['default']) && in_array($config['default'], ['EUR', 'USD', 'CHF'])) return $config['default'];

        return 'EUR';
    }

}

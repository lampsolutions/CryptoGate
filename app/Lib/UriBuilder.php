<?php

namespace App\Lib;

class UriBuilder {
    public static function buildUri($coin, $network, $address, $amount_bitcoin) {
        $proto='bitcoin:';
        switch($coin) {
            case 'BTC':
                $proto=$network == 'testnet' ? 'bitcoin:' : 'bitcoin:';
                $proto=$proto.$address;
                break;
            case 'BCH':
                try {
                    $address = CashAddress::old2new($address);
                    $proto = $address;
                } catch (\Exception $e) {}
                break;
            case 'DASH':
                $proto=$network == 'testnet' ? 'dash:' : 'dash:';
                $proto=$proto.$address;
                break;
            case 'LTC':
                $proto=$network == 'testnet' ? 'litecoin:' : 'litecoin:';
                $proto=$proto.$address;
                break;
        }

        return $proto.'?amount='.$amount_bitcoin;
    }
}

?>
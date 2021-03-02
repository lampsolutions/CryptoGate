<?php

namespace App\Lib;

use BitWasp\Bitcoin\Address\AddressCreator;
use App\Networks\Bitcoin_P2WPKH_P2SH;
use App\Networks\BitcoinCash_P2PKH;
use App\Networks\Dash_P2PKH;
use App\Networks\Dash_Testnet_P2PKH;
use App\Networks\Litecoin_P2WPKH_P2SH;
use App\Networks\Litecoin_Testnet_P2WPKH_P2SH;

class ScriptHash {
    public static function fromAddress($currency, $network, $address) {
        if($currency == 'BCH') {
            try {
                $legacy_address = CashAddress::new2old($address, true);
                $address = $legacy_address;
            } catch (\Exception $e) {}
        }

        $network = ScriptHash::getNetworkByCurrency($currency, $network);
        $addrCreator = new AddressCreator();
        $scriptPubKey = $addrCreator->fromString($address, $network)->getScriptPubKey()->getBinary();
        $hash = hash('sha256', $scriptPubKey);
        if(strlen($hash) == 63) $hash = "0".$hash; // Fix to 64 TODO maybe remove
        $scriptHash = join("", array_reverse(str_split($hash, 2)));

        return $scriptHash;
    }


    public static function getNetworkByCurrency($currency, $network) {
        if($network == "mainnet") {
            switch(strtoupper($currency)) {
                case 'BTC':
                    return new Bitcoin_P2WPKH_P2SH();
                    break;
                case 'BCH':
                    return new BitcoinCash_P2PKH();
                    break;
                case 'LTC':
                    return new Litecoin_P2WPKH_P2SH();
                    break;
                case 'DASH':
                    return new Dash_P2PKH();
                    break;
                default:
                    throw new \Exception('Missing Network Configuration for Currency');
            }
        } elseif($network == "testnet") {
            switch(strtoupper($currency)) {
                case 'BTC':
                    return new \App\Networks\Bitcoin_Testnet_P2WPKH_P2SH();
                    break;
                case 'BCH':
                    return new \App\Networks\BitcoinCash_Testnet_P2PKH();
                    break;
                case 'LTC':
                    return new Litecoin_Testnet_P2WPKH_P2SH();
                    break;
                case 'DASH':
                    return new Dash_Testnet_P2PKH();
                    break;
                default:
                    throw new \Exception('Missing Network Configuration for Currency');
            }
        } else {
            throw new \Exception('Missing Network Configuration for Currency');
        }
    }

}

?>
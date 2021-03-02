<?php

namespace App\Lib;

use App\Networks\Bitcoin_P2WPKH_P2SH;
use App\Networks\BitcoinCash_P2PKH;
use App\Networks\Dash_P2PKH;
use App\Networks\Dash_Testnet_P2PKH;
use App\Networks\Litecoin_P2WPKH_P2SH;
use App\Networks\Litecoin_Testnet_P2WPKH_P2SH;
use BitWasp\Bitcoin\Address\AddressCreator;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Adapter\EcAdapter;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\GlobalPrefixConfig;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\NetworkConfig;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix;
use BitWasp\Bitcoin\Key\Deterministic\Slip132\Slip132;
use BitWasp\Bitcoin\Key\Factory\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper;
use BitWasp\Bitcoin\Math\Math;
use BitWasp\Bitcoin\Network\Network;
use BitWasp\Bitcoin\Network\Slip132\BitcoinRegistry;
use BitWasp\Bitcoin\Network\Slip132\BitcoinTestnetRegistry;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\GeneratorPoint;

class BIP32PublicKey {
    public $currency;

    public $publicKey;

    /**
     * @var Network
     */
    protected $network;
    /**
     * @var Math
     */
    protected $math;
    /**
     * @var GeneratorPoint
     */
    protected $generator;
    /**
     * @var EcAdapterFactory
     */
    protected $ecAdapterFactory;
    /**
     * @var EcAdapter
     */
    protected $ecAdapter;
    /**
     * @var HierarchicalKeyFactory
     */
    protected $hdFactory;
    /**
     * @var KeyToScriptHelper
     */
    protected $keyToScriptHelper;
    /**
     * @var Slip132
     */
    protected $slip132;

    /**
     * @var ScriptPrefix
     */
    protected $scriptPrefix;

    /**
     * @var BitcoinRegistry
     */
    protected $bitcoinRegistry;

    /**
     * BIP32PublicKey constructor.
     * @param $publicKey
     * @param $currency
     */
    protected $addressCreator;

    protected $networkStr;

    public function __construct($publicKey, $currency, $network='mainnet') {
        $this->publicKey = $publicKey;
        $this->currency = $currency;
        $this->networkStr = $network;
        $this->initDeps();
        $this->setNetworkByCurrency();
    }

    protected function setNetworkByCurrency() {
        if($this->networkStr == "mainnet") {
            switch(strtoupper($this->currency)) {
                case 'BTC':
                    $this->network = new Bitcoin_P2WPKH_P2SH();
                    $this->scriptPrefix = $this->slip132->p2shP2wpkh($this->bitcoinRegistry);
                    break;
                case 'BCH':
                    $this->network = new BitcoinCash_P2PKH();
                    $this->scriptPrefix = $this->slip132->p2pkh($this->bitcoinRegistry);
                    break;
                case 'LTC':
                    $this->network = new Litecoin_P2WPKH_P2SH();
                    $this->scriptPrefix = $this->slip132->p2shP2wpkh($this->bitcoinRegistry);
                    break;
                case 'DASH':
                    $this->network = new Dash_P2PKH();
                    $this->scriptPrefix = $this->slip132->p2pkh($this->bitcoinRegistry);
                    break;
                default:
                    throw new \Exception('Missing Network Configuration for Currency');
            }
        } elseif($this->networkStr == "testnet") {
            switch(strtoupper($this->currency)) {
                case 'BTC':
                    $this->network = new \App\Networks\Bitcoin_Testnet_P2WPKH_P2SH();
                    $this->scriptPrefix = $this->slip132->p2shP2wpkh($this->bitcoinRegistry);
                    break;
                case 'BCH':
                    $this->network = new \App\Networks\BitcoinCash_Testnet_P2PKH();
                    $this->scriptPrefix = $this->slip132->p2pkh($this->bitcoinRegistry);
                    break;
                case 'LTC':
                    $this->network = new Litecoin_Testnet_P2WPKH_P2SH();
                    $this->scriptPrefix = $this->slip132->p2shP2wpkh($this->bitcoinRegistry);
                    break;
                case 'DASH':
                    $this->network = new Dash_Testnet_P2PKH();
                    $this->scriptPrefix = $this->slip132->p2pkh($this->bitcoinRegistry);
                    break;
                default:
                    throw new \Exception('Missing Network Configuration for Currency');
            }
        } else {
            throw new \Exception('Missing Network Configuration for Currency');
        }
        return true;
    }

    protected function initDeps() {
        $this->math = new Math();
        $this->generator = EccFactory::getSecgCurves($this->math)->generator256k1();
        $this->ecAdapterFactory = new EcAdapterFactory();
        $this->ecAdapter = $this->ecAdapterFactory::getAdapter($this->math, $this->generator);
        $this->hdFactory = new HierarchicalKeyFactory($this->ecAdapter);
        $this->keyToScriptHelper = new KeyToScriptHelper($this->ecAdapter);
        $this->slip132 = new Slip132($this->keyToScriptHelper);

        if($this->networkStr=='mainnet') {
            $this->bitcoinRegistry = new BitcoinRegistry();
        } elseif($this->networkStr=='testnet') {
            $this->bitcoinRegistry = new BitcoinTestnetRegistry();
        } else {
            throw \Exception('missing netowkr conifg');
        }

        $this->addressCreator = new AddressCreator();

    }

    public function isValidPublicKey() {
        try {
            $hKey = $this->hdFactory->fromExtended($this->publicKey, $this->network);
            return !$hKey->isPrivate();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAddressForChain($index, $chain) {
        $chain = $chain ? 1 : 0;

        $config = new GlobalPrefixConfig([
            new NetworkConfig($this->network, [$this->scriptPrefix])
        ]);

        $serializer = new Base58ExtendedKeySerializer(
            new ExtendedKeySerializer($this->ecAdapter, $config)
        );

        try {

            $key = $serializer->parse($this->network, $this->publicKey);
            $child_key = $key->derivePath((int)$chain.'/'.(int)$index);
        } catch (\Exception $e) {
            return false;
        }


        return $child_key->getAddress($this->addressCreator)->getAddress($this->network);
    }
}

?>
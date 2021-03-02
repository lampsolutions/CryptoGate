<?php

namespace App\Lib\Electrum\Notification;


use App\Lib\Electrum\Request;

class AddressNotification implements NotificationInterface {
    /**
     * @var string
     */
    private $scriptHash;

    /**
     * @var string
     */
    private $txid;

    /**
     * AddressNotification constructor.
     * @param string $scriptHash
     * @param string $txid
     */
    public function __construct($scriptHash, $txid) {
        $this->scriptHash = $scriptHash;
        $this->txid = $txid;
    }

    /**
     * @return string
     */
    public function getScriptHash() {
        return $this->scriptHash;
    }

    /**
     * @return string
     */
    public function getTxid() {
        return $this->txid;
    }

    /**
     * @return Request
     */
    public function toRequest() {
        return new Request(null, 'blockchain.scripthash.subscribe', [$this->scriptHash, $this->txid]);
    }
}
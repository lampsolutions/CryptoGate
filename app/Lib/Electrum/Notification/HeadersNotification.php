<?php

namespace App\Lib\Electrum\Notification;


use App\Lib\Electrum\Request;

class HeadersNotification implements NotificationInterface {
    /**
     * @var string
     */
    private $height;

    /**
     * @var string
     */
    private $hex;

    /**
     * AddressNotification constructor.
     * @param string $height
     * @param string $hex
     */
    public function __construct($height, $hex) {
        $this->height = $height;
        $this->hex = $hex;
    }

    /**
     * @return string
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getHex() {
        return $this->hex;
    }

    /**
     * @return Request
     */
    public function toRequest() {
        return new Request(null, 'blockchain.headers.subscribe', []);
    }
}
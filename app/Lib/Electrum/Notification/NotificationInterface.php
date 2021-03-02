<?php

namespace App\Lib\Electrum\Notification;

use App\Lib\Electrum\Request;

interface NotificationInterface {
    /**
     * @return Request
     */
    public function toRequest();
}
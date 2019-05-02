<?php

namespace App\Http\Controllers;

use App\Lib\CryptoGatePaymentService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class GiftController extends AbstractDonationController
{

    protected $index='gift';
    protected $route='Gift';

}

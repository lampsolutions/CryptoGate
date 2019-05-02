<?php

namespace App\Http\Controllers\Api;

use App\Facades\Electrum;
use App\Http\Controllers\Client\PaymentsController;
use App\Http\Controllers\Controller;
use App\Invoice;
use Illuminate\Http\Request;

class DonateFormController extends PaymentController
{
    protected $endpoint="donateForm";
}
<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Lib\CryptoGatePaymentService;
use Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class DonateController extends AbstractDonationController
{

    protected $index='donate';
    protected $route='Donate';


    public function create(){



        $donation=Request::all();

        if(
        filter_var($donation["returnUrl"], FILTER_VALIDATE_URL)
        ){
            $returnUrl=$donation["returnUrl"];
        }
        else{
            $returnUrl=route($this->route.':thankYou');
        }

        $redirect=$this->getPaymentUrl($donation,$returnUrl);

        $parts=explode("/",$redirect);
        $payment_id=array_pop($parts);


        $invoice = Invoice::where('uuid', $payment_id)->firstOrFail();

        try {
            $invoice->sendConfirmation();
        }
        catch (\Exception $e){
            return view('client.error');
        }


        return redirect (route("Donate:doubleOptIn",["uuid" => $payment_id]));
    }

    public function doubleOptIn($uuid){

        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();


        return view('client.doubleOptIn', ['title' => 'E-Mail bestätigen', 'email' => $invoice->email]);
    }

}

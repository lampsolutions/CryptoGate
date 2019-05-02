<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Lib\CryptoGatePaymentService;
use App\Mail\DonationConfirm;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

abstract class AbstractDonationController extends Controller
{

    protected $index;
    protected $route;

    public function index(){

        $prefilled=1;
        if(Request::get('amount')>0){
            $prefilled=Request::get('amount');
        }
        if(
            !empty(Request::get('returnUrl'))
            &&
            filter_var(Request::get('returnUrl'), FILTER_VALIDATE_URL)
        ){
            $returnUrl=Request::get('returnUrl');
        }
        else{
            $returnUrl=route($this->route.':thankYou');
        }
        if(!empty(Request::get('title'))){
            $title=Request::get('title');
        }
        else{
            $title="Spenden";
        }

        if(!empty(Request::get('formal'))){
            $formal=Request::get('formal');
        } else {
            $formal = 'Sie';
        }

        return view('client.'.$this->index, [
            "prefilled" => $prefilled,
            "returnUrl" => $returnUrl,
            'title' => $title,
            'route' => $this->route,
            'formal' => $formal
        ]);
    }

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


        if($redirect) {
            return redirect($redirect);
        }
        return redirect (route($this->route.":cancel"));
    }


    protected function getPaymentUrl($donation,$returnUrl) {
        /** @var CryptoGatePaymentService $service */
        $service = new CryptoGatePaymentService(
            env("API_TOKEN_MERCHANT"),
            route('donateform.create', [], false));
        $payment_url = $service->createPaymentUrl(
            $donation,
            $returnUrl,
            route($this->route.':cancel')
        );

        return $payment_url;
    }

    public function doi($uuid){

        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $invoice->doi=true;
        $invoice->ip=Request::ip();
        $invoice->optin_timestamp=date("Y-m-d H:i:s");
        $invoice->save();
        $invoice->PaymentDoiCallback(Request::ip());

        return redirect (route("payments.select",["uuid" => $uuid]));

    }


    public function thankYou(){
        return view('client.thankyou', ['title' => 'Spendenvorgang abgeschlossen']);
    }

    public function cancel(){
        return view('client.cancel',['title' => 'Spendenvorgang abgebrochen']);
    }

}

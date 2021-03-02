<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\InvoiceRequest;
use App\Lib\CryptoGatePaymentService;
use Illuminate\Http\Request;

class InvoiceRequestController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $uuid)
    {
        $invoiceRequest = InvoiceRequest::where('uuid', $uuid)->firstOrFail();

        return view('client.request', [
            'invoiceRequest' => $invoiceRequest,
            'title' => $invoiceRequest->memo,
            'formal' => 'du',
            'prefilled' => 1
        ]);
    }

    public function create(Request $request, $uuid){
        $invoiceRequest = InvoiceRequest::where('uuid', $uuid)->firstOrFail();

        $data=$request->all();
        $data['Betrag'] = $invoiceRequest->amount;
        $data['title'] = $invoiceRequest->memo;
        $data['currency'] = $invoiceRequest->currency;
        $data['return_url'] = $invoiceRequest->return_url;

        $redirect=$this->getPaymentUrl($data,$invoiceRequest->return_url);

        if($redirect) {
            return redirect($redirect);
        }
    }


    protected function getPaymentUrl($data,$returnUrl) {
        /** @var CryptoGatePaymentService $service */
        $service = new CryptoGatePaymentService(
            env("API_TOKEN_MERCHANT"),
            route('PaymentForm.create'));
        $payment_url = $service->createPaymentUrl(
            $data,
            $returnUrl,
            ''
        );

        return $payment_url;
    }

}
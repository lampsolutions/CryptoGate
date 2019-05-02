<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceCurrency;
use Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Redirect;

class PaymentsController extends Controller
{
    protected $rules = [
        'first_name'          => 'max:60',
        'last_name'          => 'max:60',
        'email'          => 'max:60',
    ];

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
    public function index(Request $request, $uuid, $currency)
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $currency = strtoupper($currency);
        $invoicePayment = $invoice->payment($currency);

        if($invoice->status == 'Paid') {
            if(!empty($invoice->return_url)) {
                return redirect(
                    $this->getReturnUrl($invoice)
                );
            }

            return view('client.paid', [
                'invoice' => $invoice,
                'title' => $invoice->memo
            ]);
        }

        return view('client.pay', [
            'invoice' => $invoice,
            'invoicePayment' => $invoicePayment,
            'legacy' => !empty($request->get('legacy')),
            'title' => $invoice->memo
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request, $uuid)
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();

        if($invoice->status == 'Paid') {
            return view('client.paid', [
                'invoice' => $invoice,
                'title' => $invoice->memo
            ]);
        }

        return view('client.select', [
            'invoice' => $invoice,
            'title' => $invoice->memo
        ]);
    }


    public function paper_image(Request $request, $uuid, $currency)
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $currency = strtoupper($currency);

        return response($invoice->getPaperQRCodePNGImage($currency), 200, ['Content-Type' => 'image/png']);
    }

    public function paper_bip70_handler(Request $request, $uuid, $currency)
    {
        /**
         * @var Invoice $invoice
         * @var InvoiceCurrency $invoiceCurrency
         */

        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $currency = strtoupper($currency);
        $invoicePayment = $invoice->payment($currency);

        return response($invoicePayment->getBIP70Data(), 200, ['Content-Type' => $invoice->getBIP70ContentTypeHeader($currency)]);
    }


    public function checkPayment(Request $request, $uuid)
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();

        return [
            'paid' => $invoice->status == 'Paid'
        ];
    }

    /**
     * @param $invoice
     * @return string
     */
    private function  getReturnUrl($invoice) {
            $parsed_url=parse_url($invoice->return_url);
            $params=[];
            @parse_str(@$parsed_url['query'],$params);
            $extra_data = \json_decode($invoice->extra_data, true);

            if(!empty($params["uuid"])){
                abort(409, 'uuid is not a valid query param for return url .');
            }
            if(!empty($params["token"])){
                abort(409, 'token is not a valid query param for return url .');

            }
            if(!empty($params["status"])){
                abort(409, 'status is not a valid query param for return url .');
            }


            //add extra data from invoice
            $params["uuid"]=$invoice->uuid;
            $params["token"]=$extra_data['token'];
            $params["status"]="Paid";
            $parsed_url['query']=http_build_query($params);

            $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
            $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
            $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
            $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
            $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
            $pass     = ($user || $pass) ? "$pass@" : '';
            $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
            $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
            $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

            return "$scheme$user$pass$host$port$path$query$fragment";
        }


}
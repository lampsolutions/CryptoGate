<?php

namespace App\Http\Controllers\Api;

use App\Facades\Electrum;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoicePayment;
use App\PaymentAddressAllocation;
use Illuminate\Http\Request;

abstract class PaymentController extends Controller
{
    protected $endpoint="__abstract__";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function create(Request $request)
    {
        if(!in_array(strtoupper($request->input('currency')), ['EUR', 'USD', 'CHF'])) {
            throw new \Exception('Incorrect currency');
        }

        // Detect test payments on shopware / woocommerce endpoints
        if( $request->input('currency') == "EUR" &&
            $request->input('return_url') == "__not_set__" &&
            in_array($this->endpoint, ['shopware', 'woocommerce'])
        ) {
            $isTestPayment = true;
        } else { // Check for ALLOW_OTHER_FIAT
            if(empty(env('ALLOW_OTHER_FIAT'))) {
                $selectedFiatCurrency = env('FIAT_CURRENCY');
                if(!empty($selectedFiatCurrency)) {
                    if($request->input('currency') != $selectedFiatCurrency) {
                        throw new \Exception('Incorrect currency');
                    }
                }
            }
        }

        // Currency Handling
        $enabled_currencies = Electrum::getEnabledCurrencies();
        $selected_currencies = $request->input('selected_currencies');

        if(!empty($selected_currencies) && !is_array($selected_currencies)) {
            $selected_currencies = explode(',', $selected_currencies);
        }

        if(empty($selected_currencies)) $selected_currencies = $enabled_currencies;
        if(is_array($selected_currencies)) {
            $selected_currencies = array_map('strtoupper', $selected_currencies);
            foreach($selected_currencies as $k => $c) {
                if(!in_array($c, $enabled_currencies)) unset($selected_currencies[$k]);
            }
        }

        $invoice = new Invoice();
        $invoice->uuid = \Webpatser\Uuid\Uuid::generate();
        $invoice->amount = $request->input('amount');
        $invoice->currency = strtoupper($request->input('currency'));
        $invoice->selected_currencies = implode(',', $selected_currencies);
        $invoice->memo = $request->input('memo');
        $invoice->note = @$request->input('note');
        $invoice->seller_name = $request->input('seller_name');
        $invoice->first_name = $request->input('first_name');
        $invoice->last_name = $request->input('last_name');
        $invoice->email = $request->input('email');
        $invoice->return_url = $request->input('return_url');
        $invoice->cancel_url = $request->input('cancel_url');
        $invoice->callback_url = $request->input('callback_url');
        $invoice->ipn_url = $request->input('ipn_url');
        $invoice->extra_data = \json_encode($request->all());
        $invoice->endpoint = $this->endpoint;
        $invoice->endpoint_version =
            $request->input('plugin_version')
                ? $request->input('plugin_version')
                : "unknown";

        switch($this->endpoint) {
            case 'shopware':
                $invoice->sw_version =
                    $request->input('shopware_version')
                        ? $request->input('shopware_version')
                        : "unknown";
                break;
            case 'woocommerce':
                $invoice->sw_version =
                    $request->input('wordpress_version')
                        ? $request->input('wordpress_version')
                        : "unknown";
                break;
            default:
                $invoice->sw_version = 'unknown';
                break;
        }

        $invoice->save();


        return [
            'payment_url' => route('payments.select', ['uuid' => $invoice->uuid] ),
            'uuid' => (string)$invoice->uuid
        ];
    }

    public function verify(Request $request) {
        $uuid = $request->input('uuid');

        /**
         * @var $invoice Invoice
         * @var $invoicePayment InvoicePayment
         * @var $paymentAddressAllocation PaymentAddressAllocation
         */
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        $invoicePayment = $invoice->InvoicePayment()->first();
        $inBlock = 0;
        if($invoicePayment) {
            $paymentAddressAllocation = $invoicePayment->paymentAddressAllocation();
            if($paymentAddressAllocation) {
                $inBlock = $paymentAddressAllocation->block;
            }
        }

        $extra_data = \json_decode($invoice->extra_data, true);

        return [
            'status' => $invoice->status,
            'inBlock' => $inBlock,
            'token' => $extra_data['token'],
            'uuid' => (string)$invoice->uuid
        ];
    }

    public function list()
    {
        $invoices = Invoice::all();

        return [
            'invoices' => $invoices->toJson()
        ];
    }

    public function supported_currencies(Request $request)
    {

        return [
            'currencies' => Electrum::getEnabledCurrencies(),
            'image' => 'data:image/png;base64,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'
        ];
    }

}

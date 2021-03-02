<?php

namespace App\Jobs;

use App\Invoice;
use App\InvoicePayment;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallback implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Invoice
     */
    protected $invoice;

    protected $callbackType;

    protected $arg1 = false;

    public function __construct(Invoice $invoice, $callbackType, $arg1=false) {
        $this->invoice = $invoice;
        $this->callbackType = $callbackType;
        $this->arg1 = $arg1;
    }

    public function handle() {
        if(method_exists($this, $this->callbackType)) {
            $method = $this->callbackType;
            $this->$method($this->arg1);
        }
    }

    public function TxPaidUserCb($arg1) {
        if(empty($this->invoice->callback_url)) {
            return false;
        }

        if (!filter_var($this->invoice->callback_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $callbackUrl=$this->getCallbackUrl();
        if(empty($callbackUrl)) {
            return false;
        }

        try {
            $client = new Client(['cookies' => true]);
            $response = $client->post($callbackUrl, [
                RequestOptions::JSON => $this->invoice
            ]);
            Log::info('User Callback Response', ['url' => $callbackUrl, 'statusCode' => $response->getStatusCode()]);
            return true;
        } catch (\Exception $e) {
            Log::warning('User Callback failed', ['error' => $e->getMessage()]);
        }
        return false;
    }

    public function IPNUserCb($arg1) {
        if(empty($this->invoice->ipn_url)) {
            return false;
        }

        if (!filter_var($this->invoice->ipn_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $ipnUrl=$this->getIPNUrl();
        if(empty($ipnUrl)) {
            return false;
        }

        try {
            $client = new Client(['cookies' => true]);
            $response = $client->get($ipnUrl);

            if($response->getStatusCode() != 200) {
                Log::warning('IPN Callback failed', ['url' => $ipnUrl, 'statusCode' => $response->getStatusCode(), 'content' => $response->getBody()->getContents()]);
                return false;
            }

            $jsonResponse = \json_decode($response->getBody()->getContents(), true);
            if(!$jsonResponse) {
                Log::warning('IPN Callback failed Response Text', ['url' => $ipnUrl, 'statusCode' => $response->getStatusCode(), 'content' => $response->getBody()->getContents() ]);
                return false;
            }

            Log::info('IPN Callback success', ['statusCode' => $response->getStatusCode(), 'content' => $jsonResponse] );
            return true;
        } catch (\Exception $e) {
            Log::warning('IPN Callback failed', ['error' => $e->getMessage()]);
        }
        return false;
    }

    public function TxPaidCb($arg1) {
        $uri=env('CALLBACK_TX_DONE');
        if(empty($uri)) return false;
        return $this->_sendCallbackTX($uri);
    }

    public function TxDoiCb($arg1) {
        $uri=env('CALLBACK_DOI');
        if(empty($uri)) return false;
        $this->invoice->dioIp=$arg1;
        return $this->_sendCallbackTX($uri);
    }

    public function TxOverpaidCb($arg1) {
        $uri=env('CALLBACK_TX_OVERPAID');
        if(empty($uri)) return false;

        /**
         * @var $invoicePayment InvoicePayment
         */
        $invoicePayment = $this->invoice->InvoicePayment()->first();
        $this->invoice->pendingSatoshi = $invoicePayment->getPendingSatoshiAmount();
        $this->invoice->receivedSatoshi = $invoicePayment->getReceivedSatoshiAmount();

        return $this->_sendCallbackTX($uri);
    }

    public function TxPartialCb($arg1) {
        $uri=env('CALLBACK_TX_PARTIAL');
        if(empty($uri)) return false;

        /**
         * @var $invoicePayment InvoicePayment
         */
        $invoicePayment = $this->invoice->InvoicePayment()->first();
        $this->invoice->pendingSatoshi = $invoicePayment->getPendingSatoshiAmount();
        $this->invoice->receivedSatoshi = $invoicePayment->getReceivedSatoshiAmount();

        return $this->_sendCallbackTX($uri);
    }

    private function getCallbackUrl() {
        $parsed_url=parse_url($this->invoice->callback_url);
        $params=[];
        @parse_str(@$parsed_url['query'],$params);
        $extra_data = \json_decode($this->invoice->extra_data, true);

        if(!empty($params["uuid"])){
            return false;
        }
        if(!empty($params["token"])){
            return false;
        }
        if(!empty($params["status"])){
            return false;
        }

        //add extra data from invoice
        $params["uuid"]=$this->invoice->uuid;
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


    private function getIPNUrl() {
        $parsed_url=parse_url($this->invoice->ipn_url);
        $params=[];
        @parse_str(@$parsed_url['query'],$params);
        $extra_data = \json_decode($this->invoice->extra_data, true);

        if(!empty($params["uuid"])){
            return false;
        }
        if(!empty($params["token"])){
            return false;
        }
        if(!empty($params["status"])){
            return false;
        }

        //add extra data from invoice
        $params["uuid"]=$this->invoice->uuid;
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

    protected function _sendCallbackTX($uri) {
        try {
            $client = new Client();
            $client->post($uri, [
                RequestOptions::JSON => $this->invoice
            ]);
            return true;
        } catch (\Exception $e) {
            Log::warning('Generic Callback failed', ['error' => $e->getMessage()]);
        }
        return false;
    }

}

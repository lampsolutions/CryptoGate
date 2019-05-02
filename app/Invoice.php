<?php

namespace App;

use App\Mail\DonationConfirm;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class Invoice extends Model
{
    protected $fillable = [
        'uuid',
        'status',
        'payment_id',
        'memo',
        'email',
        'first_name',
        'last_name',
        'seller_name',
        'amount',
        'currency',
        'return_url',
        'cancel_url',
        'callback_url',
        'extra_data',
        'expires_at',
        'optin_timestamp'
    ];

    public function setAsPaidByPayment(InvoicePayment $invoicePayment) {
        if($this->status == 'Paid') return true;

        $this->status = 'Paid';
        $this->payment_id = $invoicePayment->id;
        $this->save();
        $this->refresh();

        $this->PaymentDoneCallback();
        $this->sendCallback();
    }

    public function sendCallback(){
        if(empty($this->callback_url)) return false;


        if (!filter_var($this->callback_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $callbackUrl=$this->getCallbackUrl();

        try {
            $client = new Client();
            $client->post($callbackUrl, [
                RequestOptions::JSON => $this
            ]);
            return true;
        }catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return false;
    }

    public function sendConfirmation(){
        if($this->endpoint=="donateForm" && filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            Mail::to($this->email)->send(new DonationConfirm($this));
        }
    }

    public function PaymentDoneCallback() {
        $uri=env('CALLBACK_TX_DONE');
        if(empty($uri)) return false;

        try {
            $client = new Client();
            $client->post($uri, [
                RequestOptions::JSON => $this
            ]);
            return true;
        }catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return false;
    }

    public function PaymentDoiCallback($ip) {
        $uri=env('CALLBACK_DOI');
        if(empty($uri)) return false;

        $this->dioIp=$ip;

        try {
            $client = new Client();
            $client->post($uri, [
                RequestOptions::JSON => $this
            ]);
            return true;
        }catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return false;
    }

    public function InvoicePayment() {
        return $this->belongsTo('App\InvoicePayment', 'payment_id', 'id');
    }

    public function getExchange($amount, $destination, $origin='EUR') {
        $response = @file_get_contents(env('PRICE_EXCHANGE_API')."/api/v1/calculate-exchange?amount=$amount&origin=$origin&destination=$destination&api=coinmarketcap.com");
        $result = \json_decode($response);
        return $result;
    }

    public function getPaperQRCodePNGImage($currency) {
        $bip70_prefix='bitcoin:?r=';

        switch($currency) {
            case 'BTC':
                $bip70_prefix='bitcoin:?r=';
                break;
            case 'BCH':
                $bip70_prefix='bitcoincash:?r=';
                break;
            case 'DASH':
                $bip70_prefix='dash:?r=';
                break;
            case 'LTC':
                $bip70_prefix='litecoin:?r=';
                break;
        }

        $qrCode = new QrCode($bip70_prefix.route('payments.paper', ['uuid' => $this->uuid, 'currency' => $currency]));
        $this->setQRCodeDefaultSettings($qrCode, $currency);

        header('Content-Type: '.$qrCode->getContentType());

        return $qrCode->writeString();
    }

    public function getFormattedAmount() {
        return number_format($this->amount, 2, ',', '.');
    }

    public function payment($currency) {
        try {
            $invoicePayment = InvoicePayment::where(
                ['invoice_id' => $this->id,
                    'currency' => $currency])
                ->where('electrum_expires_at', '>=', date('Y-m-d H:i:s'))
                ->firstOrFail();

            //throw new \Exception('Expired');
            if(time() > strtotime($invoicePayment->electrum_expires_at)) {
                throw new \Exception('Expired');
            }
        } catch (\Exception $e) {
            $invoicePayment = new InvoicePayment();
            $invoicePayment->invoice_id = $this->id;
            $invoicePayment->uuid = \Webpatser\Uuid\Uuid::generate()->string;
            $invoicePayment->currency = $currency;

            $invoicePayment->createPaymentRequestOnElectrum();

            $invoicePayment->save();

        }

        if($invoicePayment) return $invoicePayment;
    }


    protected function setQRCodeDefaultSettings(QrCode &$qrCode, $currency) {
        $qrCode->setSize(300);

        $qrCode->setLogoSize(128, 128);

        switch($currency) {
            case 'BTC':
                $qrCode->setLogoPath(__DIR__.'/../resources/img/btc.png');
                break;
            case 'LTC':
                $qrCode->setLogoPath(__DIR__.'/../resources/img/ltc.png');
                break;
            case 'BCH':
                $qrCode->setLogoPath(__DIR__.'/../resources/img/bch.png');
                $qrCode->setLogoSize(128, 107);
                break;
            case 'DASH':
                $qrCode->setLogoPath(__DIR__.'/../resources/img/dash.png');
                break;
        }

        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(true);

        // https://github.com/endroid/qr-code/issues/107
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::QUARTILE));
        $qrCode->setEncoding('UTF-8');

        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
    }

    public function getBIP70ContentTypeHeader($currency) {
        switch($currency) {
            case 'BTC':
                return 'application/bitcoin-paymentrequest';
                break;
            case 'DASH':
                return 'application/dash-paymentrequest';
                break;
            case 'BCH':
                return 'application/bitcoin-paymentrequest';
                break;
            case 'LTC':
                return 'application/litecoin-paymentrequest';
                break;
        }

        return 'application/bitcoin-paymentrequest';
    }

    public function toArray() {
        $data = parent::toArray();

        if ($this->payment_id) {
            $data['invoice_payment'] = $this->InvoicePayment()->first()->toArray();
        } else {
            $data['invoice_payment'] = null;
        }

        if(!empty($this->extra_data)) {
            try {
                $data['invoice_data'] = \json_decode($this->extra_data);
            } catch (\Exception $e) {
                $data['invoice_data']=null;
            }
        }

        return $data;
    }

    private function getCallbackUrl() {
        $parsed_url=parse_url($this->callback_url);
        $params=[];
        @parse_str(@$parsed_url['query'],$params);
        $extra_data = \json_decode($this->extra_data, true);

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
        $params["uuid"]=$this->uuid;
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

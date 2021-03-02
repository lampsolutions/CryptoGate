<?php

namespace App;

use App\Facades\Electrum;
use App\Jobs\ProcessPaymentCallback;
use App\Mail\DonationConfirm;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Invoice extends Model {
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
        'selected_currencies',
        'return_url',
        'cancel_url',
        'callback_url',
        'extra_data',
        'expires_at',
        'optin_timestamp'
    ];


    public function setAsPendingTxByPayment(InvoicePayment $invoicePayment) {
        $this->handleStateChange($invoicePayment, 'Open');
        dispatch(new ProcessPaymentCallback($this, 'TxPartialCb'));
    }

    public function setAsPaidByPayment(InvoicePayment $invoicePayment) {
        if($this->status == 'Paid') return true;
        $this->handleStateChange($invoicePayment, 'Paid');
        dispatch(new ProcessPaymentCallback($this, 'TxPaidCb'));
        dispatch(new ProcessPaymentCallback($this, 'TxPaidUserCb'))->delay(now()->addSeconds(30));
        $this->handleIpnCallbacks();
    }

    public function setAsOverpaidByPayment(InvoicePayment $invoicePayment) {
        if($this->status == 'Paid') return true;
        $this->handleStateChange($invoicePayment, 'Paid');
        dispatch(new ProcessPaymentCallback($this, 'TxOverpaidCb'));
        dispatch(new ProcessPaymentCallback($this, 'TxPaidUserCb'))->delay(now()->addSeconds(30));
        $this->handleIpnCallbacks();
    }

    public function handleIpnCallbacks() {
        if(empty($this->ipn_url)) return;

        ProcessPaymentCallback::dispatch($this, 'IPNUserCb')->delay(now()->addSeconds(30));
        ProcessPaymentCallback::dispatch($this, 'IPNUserCb')->delay(now()->addHours(2));
        ProcessPaymentCallback::dispatch($this, 'IPNUserCb')->delay(now()->addHours(4));
        ProcessPaymentCallback::dispatch($this, 'IPNUserCb')->delay(now()->addHours(8));
        ProcessPaymentCallback::dispatch($this, 'IPNUserCb')->delay(now()->addHours(24));
    }

    protected function handleStateChange(InvoicePayment $invoicePayment, $status='Open') {
        $this->status = $status;
        $this->payment_id = $invoicePayment->id;
        $this->save();
        $this->refresh();
    }

    public function getFiatCurrency() {
        return $this->currency;
    }

    public function sendConfirmation(){
        if($this->endpoint=="donateForm" && filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            Mail::to($this->email)->send(new DonationConfirm($this));
        }
    }

    public function InvoicePayment() {
        return $this->belongsTo('App\InvoicePayment', 'payment_id', 'id');
    }

    public function getBestExchange($amount, $destination, $origin='EUR') {
        $response = @file_get_contents(env('PRICE_EXCHANGE_API')."/api/v1/calculate-best-exchange?amount=$amount&origin=$origin&destination=$destination");
        $result = \json_decode($response);
        return $result;
    }

    public function getExchange($amount, $destination, $origin='EUR') {
        if(!empty(env('ENABLE_BEST_PRICE_API'))) {
            return $this->getBestExchange($amount, $destination, $origin);
        }

        // Handle Custom Apis
        $api = 'coinmarketcap.com';
        try {
            if(!empty(env('CUSTOM_FIAT'))) {
                $api = env($destination . '_EXCHANGE_SOURCE');
                if ($origin == 'USD' || $origin == 'CHF') {
                    $api = env($destination . '_EXCHANGE_SOURCE_' . $origin);
                }

                if (empty($api)) {
                    $api = 'coinmarketcap.com';
                }
            }
        } catch (\Exception $e) {
            $api = 'coinmarketcap.com';
        }

        $response = @file_get_contents(env('PRICE_EXCHANGE_API')."/api/v1/calculate-exchange?amount=$amount&origin=$origin&destination=$destination&api=$api");
        $result = \json_decode($response);

        // In case of error use coinmarketcap.com as fallback
        if(empty($result)) {
            $api = 'coinmarketcap.com';
            $response = @file_get_contents(env('PRICE_EXCHANGE_API')."/api/v1/calculate-exchange?amount=$amount&origin=$origin&destination=$destination&api=$api");
            $result = \json_decode($response);
        }

        return $result;
    }

    public function getPaperQRCodePNGImage($currency) {
        $bip70_prefix='bitcoin:?r=';

        switch($currency) {
            case 'BTX':
                $bip70_prefix='bitcore:?r=';
                break;
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

            if($invoicePayment->createPaymentRequestOnElectrum()) {
                $invoicePayment->save();
                return $invoicePayment;
            } else {
                return false;
            }

        }

        return $invoicePayment;
    }


    protected function setQRCodeDefaultSettings(QrCode &$qrCode, $currency) {
        $qrCode->setSize(300);

        $qrCode->setLogoSize(128, 128);

        switch($currency) {
            case 'BTX':
                $qrCode->setLogoPath(__DIR__.'/../resources/img/btx.png');
                $qrCode->setLogoSize(128, 128);
                break;
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
            case 'BTX':
                return 'application/bitcore-paymentrequest';
                break;
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

    public function getCurrencies() {
        $enabled_currencies = Electrum::getEnabledCurrencies();
        $selected_currencies = $this->selected_currencies;
        if(empty($selected_currencies)) return $enabled_currencies; // Backwards compatibility
        $selected_currencies = explode(",", $selected_currencies);

        if(is_array($selected_currencies)) {
            $selected_currencies = array_map('strtoupper', $selected_currencies);
            foreach($selected_currencies as $k => $c) {
                if(!in_array($c, $enabled_currencies)) unset($selected_currencies[$k]);
            }
        }
        $selected_currencies = array_values($selected_currencies);
        if(empty($selected_currencies)) {
            return $enabled_currencies;
        }

        return $selected_currencies;
    }

    public function isCurrencyEnabled($currency) {
        return in_array($currency, $this->getCurrencies());
    }
}

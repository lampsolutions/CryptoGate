<?php

namespace App;

use App\Facades\Electrum;
use App\Lib\CashAddress;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Graze\GuzzleHttp\JsonRpc\Client;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'uuid',
        'currency',
        'electrum_address',
        'electrum_id',
        'electrum_uri',
        'electrum_amount',
        'electrum_expires_at'
    ];


    public function invoice() {
        return $this->belongsTo('App\Invoice');
    }

    public function parse_query($str, $urlEncoding = true)
    {
        $result = [];

        if ($str === '') {
            return $result;
        }

        if ($urlEncoding === true) {
            $decoder = function ($value) {
                return rawurldecode(str_replace('+', ' ', $value));
            };
        } elseif ($urlEncoding === PHP_QUERY_RFC3986) {
            $decoder = 'rawurldecode';
        } elseif ($urlEncoding === PHP_QUERY_RFC1738) {
            $decoder = 'urldecode';
        } else {
            $decoder = function ($str) { return $str; };
        }

        foreach (explode('&', $str) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = $decoder($parts[0]);
            $value = isset($parts[1]) ? $decoder($parts[1]) : null;
            if (!isset($result[$key])) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }

        return $result;
    }

    public function build_query(array $params, $encoding = PHP_QUERY_RFC3986)
    {
        if (!$params) {
            return '';
        }

        if ($encoding === false) {
            $encoder = function ($str) { return $str; };
        } elseif ($encoding === PHP_QUERY_RFC3986) {
            $encoder = 'rawurlencode';
        } elseif ($encoding === PHP_QUERY_RFC1738) {
            $encoder = 'urlencode';
        } else {
            throw new \InvalidArgumentException('Invalid type');
        }

        $qs = '';
        foreach ($params as $k => $v) {
            $k = $encoder($k);
            if (!is_array($v)) {
                $qs .= $k;
                if ($v !== null) {
                    $qs .= '=' . $encoder($v);
                }
                $qs .= '&';
            } else {
                foreach ($v as $vv) {
                    $qs .= $k;
                    if ($vv !== null) {
                        $qs .= '=' . $encoder($vv);
                    }
                    $qs .= '&';
                }
            }
        }

        return $qs ? (string) substr($qs, 0, -1) : '';
    }

    public function getBIP70Uri() {
        $parsed = parse_url($this->electrum_uri);
        $query = $this->parse_query($parsed['query']);
        unset($query['amount']); // drop amount

        return $parsed['scheme'].':?'.$this->build_query($query);
    }

    public function getBIP70URL() {
        $parsed = parse_url($this->electrum_uri);
        $query = $this->parse_query($parsed['query']);
        return $query['r'];
    }

    public function getLegacyUri() {
        $parsed = parse_url($this->electrum_uri);
        $query = $this->parse_query($parsed['query']);
        unset($query['r']); // drop bip70

        if($this->currency == 'BCH') {
            $parsed['path'] = CashAddress::new2old($parsed['path'], true);
        }

        return $parsed['scheme'].':'.$parsed['path'].'?'.$this->build_query($query);
    }

    protected function setQRCodeDefaultSettings(QrCode &$qrCode) {
        $qrCode->setSize(300);
        $qrCode->setLogoSize(128, 128);

        switch($this->currency) {
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

        $qrCode->setMargin(0);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(true);

        // https://github.com/endroid/qr-code/issues/107
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::QUARTILE));
        $qrCode->setEncoding('UTF-8');

        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
    }

    public function getPaperQRCodeImage() {
        $qrCode = new QrCode($this->getBIP70Uri());



        $this->setQRCodeDefaultSettings($qrCode);


        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        //$qrCode->getContentType();
    }

    public function getQRCodeDataUri($uri=false) {
        if(!$uri) $uri = $this->electrum_uri;

        $qrCode = new QrCode($uri);
        $this->setQRCodeDefaultSettings($qrCode);

        try {
            return $qrCode->writeDataUri();
        } catch (\Exception $e) {}

        // Try Disabling Logo
        try {
            $qrCode->setLogoSize(0, 0);
            return $qrCode->writeDataUri();
        } catch (\Exception $e) {}

        // Try Disabling Logo and increase uri size
        try {
            $uri .= '&ts='.time();
            $qrCode = new QrCode($uri);
            $this->setQRCodeDefaultSettings($qrCode);
            $qrCode->setLogoSize(0, 0);
            return $qrCode->writeDataUri();
        } catch (\Exception $e) {}

        return '';
    }

    public function getBIP70Data() {
        return file_get_contents($this->getBIP70URL());
    }

    public function getFullCurrencyAmount() {
        return bcdiv((string) $this->electrum_amount, (string) 1e8, 8);
    }

    public function getUnixExpirationTime() {
        return strtotime($this->electrum_expires_at);
    }

    public function getExpirationCountdown() {
        $seconds_left = strtotime($this->electrum_expires_at) -time();

        return date('i\m s\s', $seconds_left);
    }

    public function getExpirationProgressPercent() {
        $seconds_left = strtotime($this->electrum_expires_at) -time();
        $percent = ( (60*15) - $seconds_left) / (60*15) * 100;

        return (int)$percent;
    }

    public function createPaymentRequestOnElectrum() {
        $expires_at = strtotime('+15 Minutes');

        /** @var Client $client */
        $client = Electrum::client($this->currency);

        $invoice = $this->invoice()->firstOrFail();

        $response = $client->send(
            $client->request(sha1(microtime()), 'addrequest', [
                'amount' => (string)$invoice->getExchange($invoice->amount, $this->currency, $invoice->currency)->amount,
                'memo' => (string)$invoice->uuid,
                'expiration' => $expires_at,
                'force' => true
            ])
        );

        $error_code =$response->getRpcErrorCode();

        if(!is_null($error_code)) {
            return false;
        }

        $result = $response->getRpcResult();

        $this->electrum_id = $result['id'];
        $this->electrum_address = $result['address'];
        $this->electrum_amount = $result['amount'];
        $this->electrum_uri= $result['URI'];
        $this->electrum_expires_at = date("Y-m-d H:i:s", $expires_at);

        return true;
    }

}
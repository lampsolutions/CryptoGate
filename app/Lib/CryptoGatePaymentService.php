<?php
namespace App\Lib;

use Illuminate\Support\Facades\App;

class CryptoGatePaymentService {

    protected $apiKey;
    protected $apiUrl;

    public function __construct(
        $pApiKey,
        $pApiUrl){
        $this->apiKey=$pApiKey;
        $this->apiUrl=$pApiUrl;
    }

    /**
     * @param array $payment_data
     * @return string
     */
    public function createPaymentToken($payment_data)
    {
        return sha1("salt_12adasd".json_encode($payment_data));
    }

    public function createPaymentUrl($order,$returnurl,$cancelurl) {

        $parameters['token'] = $this->createPaymentToken($order);
        $parameters['api_key'] = $this->apiKey;
        $parameters["amount"] = $order["Betrag"];
        $parameters["currency"] = "EUR";


        if(empty($order['title'])) $order['title'] = env("COMPANY");
        $parameters["memo"] = $order['title'];
        //$parameters["seller_name"] = 'Ihre Spende bei '.$_SERVER['SERVER_NAME'];

        $parameters["first_name"] = isset($order["Vorname"]) ? $order["Vorname"] : "";
        $parameters["last_name"] = isset($order["Nachname"]) ? $order["Nachname"] : "";
        $parameters["email"] = isset($order["Email"]) ? $order["Email"] : "";

        $parameters["return_url"] = $returnurl;
        $parameters["cancel_url"] = $cancelurl;

        $parameters["selected_currencies"]="BTC,LTC,DASH,BCH";

        $parameters["Firmenname"] = isset($order["Firmenname"]) ? $order["Firmenname"] : "";
        $parameters["Straße"] = isset($order["Straße"]) ? $order["Straße"] : "";
        $parameters["Ort"] = isset($order["Ort"]) ? $order["Ort"] : "";
        $parameters["Land"] = isset($order["Land"]) ? $order["Land"] : "";
        $parameters["Telefon"] = isset($order["Telefon"]) ? $order["Telefon"] : "";

        $ch = curl_init('http://localhost/'.$this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = $response = curl_exec($ch);

        return json_decode($response, true)['payment_url'];
    }

    public function validatePayment() {

        $parameters=[
            "uuid" => $_GET["uuid"],
            "token" => $_GET["token"],
            "status" => $_GET["status"]
        ];

        $parameters['api_key'] = $this->apiKey;

        $ch = curl_init('http://localhost/'.$this->apiUrl."/verify");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

        $response = $response = curl_exec($ch);
        curl_close($ch);


        $verify = json_decode($response, true);

        if($verify['token'] == $_GET["token"] && !empty($_GET["token"]) && !empty($verify['token'])) {
            return true;
        }

        return false;
    }
}

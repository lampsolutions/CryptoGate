<?php

namespace App\Http\Controllers\AdminApi;

use App\Facades\Electrum;
use App\Http\Controllers\Controller;
use App\Invoice;
use Graze\GuzzleHttp\JsonRpc\Client;
use Illuminate\Http\Request;

class ElectrumController extends Controller
{

    public function balance(Request $request)
    {
        $r = [];
        foreach(Electrum::getEnabledCurrencies() as $c) {
            /** @var Client $client */
            $client = Electrum::client($c);
            $response = $client->send($client->request(sha1(microtime()), 'getbalance', [] ));
            $r[$c]=$response->getRpcResult();
        }
        return $r;
    }


    public function listaddresses(Request $request)
    {
        $r = [];
        $query = [];

        foreach(['receiving', 'change', 'labels', 'frozen', 'unused', 'funded', 'balance'] as $k) {
            if(!empty($request->get($k))) $query[$k] = (bool) $request->get($k);
        }

        foreach(Electrum::getEnabledCurrencies() as $c) {
            /** @var Client $client */
            $client = Electrum::client($c);
            $response = $client->send($client->request(sha1(microtime()), 'listaddresses', $query ));
            $r[$c]=$response->getRpcResult();
        }

        return $r;
    }

}
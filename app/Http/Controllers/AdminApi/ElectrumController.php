<?php

namespace App\Http\Controllers\AdminApi;

use App\Facades\Electrum;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Wallet;
use Graze\GuzzleHttp\JsonRpc\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectrumController extends Controller {

    public function balance(Request $request) {

        //DB::enableQueryLog();
        $balances = DB::table('addresses')
            ->select('wallet_id', DB::raw('SUM(confirmed) as total_confirmed, SUM(unconfirmed) as total_unconfirmed'))
            ->groupBy('wallet_id')->get();


        $result = [];

        foreach($balances as $balance) {
            /**
             * @var Wallet $wallet
             */
            $wallet = Wallet::where('id', $balance->wallet_id)->firstOrFail();

            $result[$wallet->coin] = [
                "confirmed" => $this->to_bitcoin($balance->total_confirmed),
            ];

            if(!empty($balance->total_unconfirmed)) {
                $result[$wallet->coin]["unconfirmed"] = $this->to_bitcoin($balance->total_unconfirmed);
            }


        }
        return $result;
    }

    public function to_bitcoin(int $satoshi) : string {
        return bcdiv((string) $satoshi, (string) 1e8, 8);
    }


}
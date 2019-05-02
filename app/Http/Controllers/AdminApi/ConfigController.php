<?php

namespace App\Http\Controllers\AdminApi;

use App\Facades\Electrum;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Lib\ExportCSV;
use Graze\GuzzleHttp\JsonRpc\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{

    public function set(Request $request) {
        $key = $request->post('key');
        $value = $request->post('value');

        \Cache::forever($key, $value);

        return ['key' => $key, 'value' => $value];
    }

}
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

class TransactionsController extends Controller {

    public function listalltransactions(Request $request) {

        $invoice = Invoice::select(
            'invoices.*'
        )->orderBy('created_at', 'desc');

        $where = [];

        if(!empty($request->get('search'))) {
            $keyword=$request->get('search');
            $invoice = $invoice->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('memo', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%");
            });
        }

        //$invoice->where('payment_address_allocations.status', '=', 'partial');

        if(!empty($request->get('start'))) {
            $invoice->where('created_at', '>=', date('Y-m-d 00:00:00', (int)$request->get('start')));
        }

        if(!empty($request->get('end'))) {
            $invoice->where('created_at', '<=', date('Y-m-d 23:59:59', (int)$request->get('end')));
        }

        if(!empty($request->get('uuid'))) {
            $invoice->where('uuid', '=', (String)$request->get('uuid'));
        }


        if(!empty($request->get('endpoints')) && $request->get('endpoints') != "-1") {
            switch($request->get('endpoints')){
                case "Shopware":
                    $invoice->where('endpoint', '=', 'shopware');
                    break;
                case "Woocommerce":
                    $invoice->where('endpoint', '=', 'woocommerce');
                    break;
                case "Spende mit Kontaktdaten":
                    $invoice->where('endpoint', '=', 'donateForm')->whereNotNull('email');
                    break;
                case "Spende ohne Kontaktdaten":
                    $invoice->where('endpoint', '=', 'donateForm')->whereNull('email');
                    break;
                case "Zahlungsformulare":
                    $invoice->where('endpoint', '=', 'paymentForm');
                    break;
            }
        }

        $invoice=$invoice->paginate( (int)$request->get('paginate', 100) );
        return $invoice;
    }

    public function listtransactions(Request $request) {


        $invoice = Invoice::select(
            'invoices.*',
            'invoice_payments.currency as cp_currency',
            'invoice_payments.electrum_amount as cp_amount',
            'invoice_payments.created_at as cp_created_at',
            'invoice_payments.electrum_address as cp_address',
            DB::Raw('IFNULL(payment_address_allocations.pending, 0) as cp_pending'),
            DB::Raw('IFNULL(payment_address_allocations.received, 0) as cp_received'),
            DB::Raw('IFNULL(payment_address_allocations.status, invoices.status) as cp_status')
        )
            ->join("invoice_payments",function($join){
                $join->on("invoices.payment_id","=","invoice_payments.id")
                ->on("invoices.id","=","invoice_payments.invoice_id");
            }
        )
            ->leftJoin("payment_address_allocations",function($join){
                $join->on("invoice_payments.electrum_id","=","payment_address_allocations.id")
                    ->on("payment_address_allocations.id","=","invoice_payments.electrum_id");
            }
        )->orderBy('created_at', 'desc');

        $where = [];

        if(!empty($request->get('search'))) {
            $keyword=$request->get('search');
            $invoice = $invoice->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('memo', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%");
            });
        }

        //$invoice->where('payment_address_allocations.status', '=', 'partial');

        if(!empty($request->get('start'))) {
            $invoice->where('invoice_payments.created_at', '>=', date('Y-m-d 00:00:00', (int)$request->get('start')));
        }

        if(!empty($request->get('end'))) {
            $invoice->where('invoice_payments.created_at', '<=', date('Y-m-d 23:59:59', (int)$request->get('end')));
        }

        if(!empty($request->get('uuid'))) {
            $invoice->where('invoices.uuid', '=', (String)$request->get('uuid'));
        }

        if(!empty($request->get('currency')) && $request->get('currency') != "-1") {
            $invoice->where('invoice_payments.currency', '=', $request->get('currency'));
        }
        if(!empty($request->get('endpoints')) && $request->get('endpoints') != "-1") {
            switch($request->get('endpoints')){
                case "Shopware":
                    $invoice->where('endpoint', '=', 'shopware');
                    break;
                case "Woocommerce":
                    $invoice->where('endpoint', '=', 'woocommerce');
                    break;
                case "Spende mit Kontaktdaten":
                    $invoice->where('endpoint', '=', 'donateForm')->whereNotNull('email');
                    break;
                case "Spende ohne Kontaktdaten":
                    $invoice->where('endpoint', '=', 'donateForm')->whereNull('email');
                    break;
                case "Zahlungsformulare":
                    $invoice->where('endpoint', '=', 'paymentForm');
                    break;
            }
        }


        if(!empty($request->get('query_key'))) {
            $query_key = "";
            $query_value = "";
            $query_value_or="";
            switch($request->get('query_key')) {
                case 'address':
                    $query_key = 'invoice_payments.electrum_address';
                    $query_value = "%".(string)$request->get('query_value')."%";
                    break;
                case 'amount':
                    $query_key = 'invoices.amount';
                    $query_value = (double)$request->get('query_value');
                    break;
                case 'camount':
                    $query_key = 'invoice_payments.electrum_amount';
                    $query_value = bcmul($this->to_fixed((float) $request->get('query_value'), 8),
                        (string) 1e8);
                    break;
            }

            $query_mode = '>';
            if($request->get('query_mode') == "gt") $query_mode = '>';
            if($request->get('query_mode') == "lt") $query_mode = '<';
            if($request->get('query_mode') == "eq") $query_mode = '=';

            if($request->get('query_key') == 'address') $query_mode = 'LIKE';

            if(!empty($query_key)) $invoice->where($query_key, $query_mode, $query_value);
        }


        if(!empty($request->get('csv'))) {
            $csvExporter = new ExportCSV();
            $csvExporter->beforeEach(function ($line) {
                $line->cp_created_at = date('d.m.Y H:i:s', strtotime($line->cp_created_at));
                $line->cp_amount = number_format(bcdiv((string) $line->cp_amount, (string) 1e8, 8), 8, ',', '.');
                $line->amount = number_format($line->amount, 2, ',', '.');
            });

            return [ 'data' => (string) $csvExporter->build($invoice->get(),
                [
                    'first_name',
                    'last_name',
                    'email',
                    'amount',
                    'cp_amount',
                    'cp_status',
                    'cp_currency',
                    'cp_address',
                    'cp_created_at',
                    'seller_name',
                    'amount',
                    'currency',
                    'return_url',
                    'cancel_url',
                    'callback_url',
                    'extra_data',
                    'expires_at',
                    'endpoint',
                    'optin_timestamp'])->getCsv() ];
        };

        $invoice=$invoice->paginate( (int)$request->get('paginate', 100) );
        return $invoice;
    }

    public function listrequests(Request $request) {
        $invoice  = Invoice::with('InvoicePayment')->orderBy('created_at', 'desc')->where('status', 'Open');

        if(!empty($request->get('search'))) {
            $keyword=$request->get('search');
            $invoice = $invoice->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('memo', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%");
            });
        }

        $invoice=$invoice->paginate( (int)$request->get('paginate', 100) );
        return $invoice;
    }


    function to_fixed(float $number, int $precision = 8) : string
    {
        $number = $number * pow(10, $precision);

        return bcdiv((string) $number, (string) pow(10, $precision), $precision);
    }
}

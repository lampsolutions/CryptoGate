<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\InvoiceRequest;
use Illuminate\Http\Request;

class InvoiceRequestsController extends Controller
{

    public function get(Request $request) {
        $invoiceRequest = InvoiceRequest::where('id', $request->get('id'))->firstOrFail();
        return $invoiceRequest->toArray();
    }

    public function update(Request $request) {
        $data = $request->all();

        $invoiceRequest = InvoiceRequest::where('id', $request->get('id'))->firstOrFail();
        $invoiceRequest->fill($data);

        if($invoiceRequest->save()) {
            $invoiceRequest->refresh();
        }

        return $invoiceRequest->toArray();
    }

    public function delete(Request $request) {
        $invoiceRequest = InvoiceRequest::where('id', $request->get('id'))->firstOrFail();
        if($invoiceRequest) {
            $invoiceRequest->delete();
            return true;
        }

        return false;
    }

    public function create(Request $request) {

        $data = $request->all();

        $data['enabled'] = 1;
        $data['currency'] = empty($data['currency']) ? 'EUR' : $data['currency'];

        $invoiceRequest = new InvoiceRequest();
        $invoiceRequest->uuid = \Webpatser\Uuid\Uuid::generate()->string;
        $invoiceRequest->fill($data);

        if($invoiceRequest->save()) {
            $invoiceRequest->refresh();
        }

        return $invoiceRequest->toArray();
    }

    public function list(Request $request) {
        $invoice  = InvoiceRequest::orderBy('created_at', 'desc');

        if(!empty($request->get('search'))) {
            $keyword=$request->get('search');
            $invoice = $invoice->where(function ($q) use ($keyword) {
                $q->where('memo', 'LIKE', "%$keyword%")
                    ->orWhere('name', 'LIKE', "%$keyword%")
                    ->orWhere('memo', 'LIKE', "%$keyword%");
            });
        }

        $invoice=$invoice->paginate( (int)$request->get('paginate', 100) );
        return $invoice;
    }
}
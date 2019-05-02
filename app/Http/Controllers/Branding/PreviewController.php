<?php

namespace App\Http\Controllers\Branding;

use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoicePayment;
use Illuminate\Http\Request;

class PreviewController extends Controller
{

    public function index(Request $request) {
        $branding = $request->all();

        // Create Fake Payments
        $invoicePayment = new InvoicePayment();
        $invoice = new Invoice();

        // Fake Invoice
        $invoice->memo = 'Transaktion '.uniqid();
        $invoice->amount = 0.99;
        $invoice->status = 'Paid';


        // Fake invoicePayment
        $invoicePayment->currency = 'BTC';
        $invoicePayment->uuid = 'test';
        $invoicePayment->electrum_id = 1;
        $invoicePayment->electrum_uri = 'bitcoin:3Gy8jJrBQ2YGXucBX4XUfmEfZ3aVgH2G56?amount=0.00087461&r=https://demo/';
        $invoicePayment->electrum_amount = 87461;
        $invoicePayment->electrum_address = '3Gy8jJrBQ2YGXucBX4XUfmEfZ3aVgH2G56';
        $invoicePayment->electrum_expires_at = date('Y-m-d H:i:s', strtotime('+30minutes'));



        return view('client.pay', [
            'title' => $invoice->memo,
            'invoice' => $invoice,
            'invoicePayment' => $invoicePayment,
            'branding' => $branding,
        ]);
    }

}
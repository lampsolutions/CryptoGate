<?php

namespace App\Console\Commands;

use App\Facades\Electrum;
use App\Invoice;
use App\InvoicePayment;
use App\Mail\DonationConfirm;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PaymentPayedController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:setpayed {id=-1} {coin=LTC}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watches for incoming payment requests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $cache=[
        'BTC' => [],
        'LTC' => [],
        'DASH' => [],
        'BCH' => [],
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        if($id==-1){
            $invoice=Invoice::orderBy('id', 'desc')->first();
        }
        else{
            $invoice=Invoice::find($this->argument('id'));
        }
        if($invoice){
            $expires_at = strtotime('+15 Minutes');
            $invoicePayment = new InvoicePayment();
            $invoicePayment->invoice_id=$invoice->id;
            $invoicePayment->currency=$this->argument('coin');
            $invoicePayment->electrum_amount=$invoice->amount;

            $invoicePayment->electrum_id = "_test_";
            $invoicePayment->electrum_address = "_test_";
            $invoicePayment->electrum_uri= "_test_";
            $invoicePayment->electrum_expires_at = date("Y-m-d H:i:s", $expires_at);

            $invoicePayment->uuid=\Webpatser\Uuid\Uuid::generate()->string;
            $invoicePayment->save();
            $invoicePayment->refresh();
            $invoice->setAsPaidByPayment($invoicePayment);

        }
    }



}

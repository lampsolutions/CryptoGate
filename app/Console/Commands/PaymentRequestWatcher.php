<?php

namespace App\Console\Commands;

use App\Facades\Electrum;
use App\InvoicePayment;
use App\Mail\DonationConfirm;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PaymentRequestWatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:watch';

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
        while(true) {
            foreach(Electrum::getEnabledCurrencies() as $currency) {
                $this->handleCurrency($currency);
            }
            sleep(5);
        }
    }

    private function handleCurrency($currency) {
        /** @var Client $client */
        $client = Electrum::client($currency);

        $response = $client->send(
            $client->request(sha1(microtime()), 'listrequests', [
                'paid'=>true
            ])
        );

        $error_code =$response->getRpcErrorCode();

        if(!is_null($error_code)) {
            return false;
        }

        $result = $response->getRpcResult();
        foreach($result as $payment) {
            if(in_array($payment['id'], $this->cache[$currency])) continue;
            $this->cache[$currency][]=$payment['id'];

            try {
                $invoicePayment = InvoicePayment::where('electrum_id', $payment['id'])->firstOrFail();
                $invoice = $invoicePayment->invoice()->first();
                $invoice->setAsPaidByPayment($invoicePayment);

            } catch (\Exception $e) {
                continue;
            }

        }
    }
}

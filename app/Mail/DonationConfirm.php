<?php

namespace App\Mail;

use App\Invoice;
use App\Lib\CryptoGateBranding;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DonationConfirm extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('mail.doi', [
                "url" => route('Donate:doi', ["uuid"=>$this->invoice->uuid]),
            ])
            ->subject('Ihre Spende - Bestätigen Sie Ihre Daten');

    }
}

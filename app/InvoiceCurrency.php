<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceCurrency extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
    ];

    public $timestamps = false;

    /**
     * @return float
     */
    public function getAmount() {
        return (double)$this->amount;
    }

    private function getExchangeRate($amount, $origin='EUR') {
        $response = @file_get_contents(env('PRICE_EXCHANGE_API')."/api/v1/calculate-exchange?amount=$amount&origin=$origin&destination=$this->currency&api=coinmarketcap.com");
        $result = \json_decode($response);
        return $result;
    }

}
<?php

namespace App;

use App\Lib\UriBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Address
 * @package App
 * @property Integer $wallet_id
 * @property Integer $address_id
 * @property Integer $amount
 * @property Integer $pending
 * @property Integer $received
 * @property Integer $block
 * @property Integer $tx_cut_height
 * @property String $memo
 * @property String $status
 * @property String $created_at
 * @property String $expires_at
 * @property Address $address
 * @property Wallet $wallet
 */
class PaymentAddressAllocation extends Model {

    const PAYMENT_STATUS_OPEN = 'open';
    const PAYMENT_STATUS_PARTIAL_PAYMENT = 'partial';
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_OVERPAID = 'overpaid';

    public function address() {
        return $this->belongsTo(Address::class);
    }

    public function wallet() {
        return $this->belongsTo(Wallet::class);
    }

    public function to_bitcoin(int $satoshi) : string {
        return bcdiv((string) $satoshi, (string) 1e8, 8);
    }

    public function buildPaymentUri($satoshis=0) {
        if(empty($satoshis)) {
            $satoshis = $this->amount;
        }

        return UriBuilder::buildUri($this->wallet->coin, $this->wallet->network, $this->address->address, $this->to_bitcoin($satoshis));
    }
}

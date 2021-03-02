<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Address
 * @package App
 * @property String $address
 * @property String $network
 * @property Integer $history
 * @property Integer $height
 * @property Integer $hd_chain
 * @property Integer $hd_index
 * @property Integer $confirmed
 * @property Integer $unconfirmed
 * @property String $script_hash
 * @property String $created_at
 * @property Integer $wallet_id
 */
class Address extends Model {

    public function allocations() {
        return $this->hasMany(PaymentAddressAllocation::class);
    }

    /**
     * @return PaymentAddressAllocation|null
     */
    public function pendingAllocation() {
        return $this->allocations()
            ->where('expires_at', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('address_id', $this->id)
            ->orderBy('created_at', 'ASC')->first();
    }

}

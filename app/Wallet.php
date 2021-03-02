<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 * @package App
 * @property Integer $gap_limit
 * @property String $public_key
 * @property String $coin
 * @property String $network
 * @property Integer $height
 * @property String $server_dsn
 */
class Wallet extends Model {

    protected $fillable = ['id', 'coin', 'gap_limit', 'public_key', 'network', 'height', 'server_dsn'];

    public const HD_EXTERNAL_CHAIN = 0;
    public const HD_INTERNAL_CHAIN = 1;

    public function addresses() {
        return $this->hasMany(Address::class);
    }

    public function allocations() {
        return $this->hasMany(PaymentAddressAllocation::class);
    }

    public function pendingAllocationForAddress(Address $address) {
        return $this->allocations()
            ->where('expires_at', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('address_id', $address->id)
            ->orderBy('created_at', 'ASC');
    }

    public function pendingAllocations() {
        return $this->allocations()
            ->where('expires_at', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->orderBy('created_at', 'ASC');
    }

    public function internal_addresses() {
        return $this->addresses()
            ->where('hd_chain', '=', '1')
            ->orderBy('hd_index', 'ASC');
    }

    public function addresses_by_chain($chain=Wallet::HD_EXTERNAL_CHAIN) {
        return $this->addresses()
            ->where('hd_chain', '=', $chain)
            ->orderBy('hd_index', 'ASC');
    }

    /**
     * @param $script_hash
     * @return Address
     */
    public function get_address_by_script_hash($script_hash) {
        return $this->addresses()
            ->where('script_hash', '=', $script_hash)->first();
    }


    public function highest_address_with_history_by_chain($chain=Wallet::HD_EXTERNAL_CHAIN) {
        return $this->addresses()
            ->where('hd_chain', '=', $chain)
            ->where('history', '>', '0')
            ->orderBy('hd_index', 'desc')
            ->first();
    }

    public function addresses_height_by_chain($chain=Wallet::HD_EXTERNAL_CHAIN) {
        return $this->addresses_by_chain($chain)->count();
    }

    public function external_addresses() {
        return $this->addresses()
            ->where('hd_chain', '=', '0')
            ->orderBy('hd_index', 'ASC');
    }



    public function highest_external_address_with_history() {
        return $this->addresses()
            ->where('hd_chain', '=', '0')
            ->where('history', '>', '0')
            ->orderBy('hd_index', 'desc')
            ->first();
    }

    public function highest_internal_address_with_history() {
        return $this->addresses()
            ->where('hd_chain', '=', '1')
            ->where('history', '>', '0')
            ->orderBy('hd_index', 'desc')
            ->first();
    }

    public function internal_addresses_height() {
        return $this->internal_addresses()->count();
    }

    public function external_addresses_height() {
        return $this->external_addresses()->count();
    }

    public function getAvailablePaymentRequestAddress() {

    }
}

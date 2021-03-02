<?php

namespace App\Services;

use App\Address;
use App\Lib\BIP32PublicKey;
use App\Lib\Electrum\Client;
use App\Lib\Electrum\Notification\AddressNotification;
use App\Lib\Electrum\Notification\HeadersNotification;
use App\Lib\Electrum\Response;
use App\Lib\ScriptHash;
use App\Wallet;
use http\Header;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use React\Promise\Promise;

class HierarchicalDeterministicWalletService {

    /**
     * @var Wallet
     */
    protected $wallet;

    /**
     * @var Client
     */
    protected $client;

    protected $bip32;

    protected $paymentRequestService;

    public $synchronized = false;

    public function getWallet() {
        return $this->wallet;
    }

    public function __construct(Wallet $wallet, Client $client) {
        $this->wallet = $wallet;
        $this->client = $client;
        $this->paymentRequestService = new PaymentRequestService($this, $this->wallet);

        $this->bip32 = new BIP32PublicKey($this->wallet->public_key, $this->wallet->coin, $this->wallet->network);

        $client->subscribe_headers();

        $this->client->getConnection()->on('blockchain.headers.subscribe', function (HeadersNotification $headersNotification) {
            Log::info('Subscription Blockchain Header received', ['height' => $headersNotification->getHeight()]);
            $this->wallet->refresh();
            $this->wallet->height = $headersNotification->getHeight();
            $this->wallet->save();
        });

        $this->client->getConnection()->on('blockchain.scripthash.subscribe', function (AddressNotification $addressNotification) {
            Log::info('Subscription Blockchain Address received', ['height', $addressNotification->getScriptHash()]);

            /** @var Address $address */
            $address = $this->wallet->get_address_by_script_hash($addressNotification->getScriptHash());

            $promises = [];
            $promises[] = $this->update_address_history($address);
            $promises[] = $this->update_address_balance($address);

            $promise = \React\Promise\all($promises);

            $promise->then(function() use($address) {
                $pendingAllocation = $address->pendingAllocation();
                if($pendingAllocation) $this->paymentRequestService->checkPaymentStatus($pendingAllocation);
            });

        });

    }

    private function increaseGapLimitByAmountForChain($amount, $chain) {
        $addresses = [];

        for($i=0;$i < $amount; $i++) {
            $addresses[] = $this->create_new_address($chain);
        }

        return $this->updateAddresses($addresses);
    }

    public function updateAddresses($addresses) {
        $promises = [];

        foreach($addresses as $address) {
            $promises[] = $this->update_address_balance($address);
            $promises[] = $this->update_address_history($address);
            $promises[] = $this->client->subscribe_address($address->script_hash);
        }
        $promise = \React\Promise\all($promises);
        return $promise;
    }

    public function handleChainGapLimit($chain) {
        $hd_height = $this->wallet->addresses_height_by_chain($chain);

        $gap = $hd_height - $this->wallet->gap_limit;
        if($gap < 0) { // New Wallets ensure we track all addresses for initial gap limit
            $this->increaseGapLimitByAmountForChain($this->wallet->gap_limit, $chain)->then(function() use($chain) {
                $this->handleChainGapLimit($chain);
            });
            return true;
        }

        $lastAddressWithHistory = $this->wallet->highest_address_with_history_by_chain($chain);
        if($lastAddressWithHistory) { // Check Gap Limit for last address
            $current_hd_height = $this->wallet->addresses_height_by_chain($chain);
            $needed_hd_height = $lastAddressWithHistory->hd_index + 1 + $this->wallet->gap_limit;
            $gap = $needed_hd_height - $current_hd_height;

            if($gap > 0) {
                $this->increaseGapLimitByAmountForChain($gap, $chain)->then(function() use($chain, $gap) {
                    $this->handleChainGapLimit($chain);
                });
                return true;
            }
        }

        return true;
    }

    public function synchronize() {
        $addresses = $this->wallet->addresses()->get();

        $updatePromise = $this->updateAddresses($addresses);

        $updatePromise->then(function() {
            $this->handleChainGapLimit(Wallet::HD_EXTERNAL_CHAIN);
            $this->handleChainGapLimit(Wallet::HD_INTERNAL_CHAIN);
            $this->synchronized = true;
            Log::info('HD Wallet Service synchronized', ['walletid' => $this->wallet->id]);
        });

        return $updatePromise;
    }

    public function update_address_balance(Address $address) {
        if(App::environment('local', 'staging')) {
            Log::debug('Updating Address Balance Request', ['address' => $address->address, 'wallet' => $this->wallet->id]);
        }
        $promise = $this->client->get_balance($address->script_hash);
        $promise->then(function (Response $response) use ($address) {
            if(App::environment('local', 'staging')) {
                Log::debug('Updating Address Balance Response', ['address' => $address->address, 'wallet' => $this->wallet->id]);
            }
           $address->confirmed = $response->getResult()['confirmed'];
           $address->unconfirmed = $response->getResult()['unconfirmed'];
           $address->save();
        });
        return $promise;
    }

    public function update_address_history(Address $address) {
        if(App::environment('local', 'staging')) {
            Log::debug('Updating Address History Request', ['address' => $address->address, 'wallet' => $this->wallet->id]);
        }
        $promise = $this->client->get_history($address->script_hash);
        $promise->then(function (Response $response) use ($address) {
            if(App::environment('local', 'staging')) {
                Log::debug('Updating Address History Response', ['address' => $address->address, 'wallet' => $this->wallet->id]);
            }
            $address->history = count($response->getResult());
            $address->height = (int) max(array_column($response->getResult(), 'height'));
            $address->save();
        });
        return $promise;
    }

    public function create_new_address($chain) {
        $hd_height = $this->wallet->addresses_height_by_chain($chain);
        $addr = $this->bip32->getAddressForChain($hd_height, $chain);

        $address = new Address();
        $address->address = $addr;
        $address->script_hash = ScriptHash::fromAddress($this->wallet->coin, $this->wallet->network, $addr);

        $address->confirmed = 0;
        $address->unconfirmed = 0;
        $address->height = 0;
        $address->history = 0;
        $address->hd_index = $hd_height;
        $address->hd_chain = $chain;
        $address->wallet_id = $this->wallet->id;

        try {
            $address->save();
            return $address;
        } catch (\Exception$exception) {
            var_dump($exception->getMessage());
        }

        return false;
    }

}

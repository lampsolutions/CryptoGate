<?php

namespace App\Services;

use App\Address;
use App\Invoice;
use App\InvoicePayment;
use App\Lib\BIP32PublicKey;
use App\Lib\Electrum\Client;
use App\Lib\Electrum\Response;
use App\Lib\ScriptHash;
use App\PaymentAddressAllocation;
use App\Wallet;
use Illuminate\Support\Carbon;
use React\Promise\Promise;
use Illuminate\Support\Facades\Log;

class PaymentRequestService {

    /**
     * @var HierarchicalDeterministicWalletService
     */
    protected $walletService;

    /**
     * @var Wallet
     */
    protected $wallet;

    public $synchronized = false;

    public function __construct(HierarchicalDeterministicWalletService $walletService, Wallet $wallet) {
        $this->walletService = $walletService;
        $this->wallet = $wallet;
    }

    public function getWallet() {
        return $this->wallet;
    }

    public function synchronize() {
        foreach($this->wallet->pendingAllocations()->get() as $pendingAllocation) {
            $this->checkPaymentStatus($pendingAllocation, true);
        }

        Log::info('Payment Request Service synchronized', ['walletid' => $this->wallet->id]);
        $this->synchronized = true;
    }

    public function checkPaymentStatus(PaymentAddressAllocation $paymentAddressAllocation, $synchronize=false) {

        try {
            /** @var Address $address */
            $address = $paymentAddressAllocation->address()->firstOrFail();
            /** @var InvoicePayment $invoicePayment */
            $invoicePayment = InvoicePayment::where('electrum_id', (string)$paymentAddressAllocation->id)->firstOrFail();
            /** @var Invoice $invoice */
            $invoice = $invoicePayment->invoice()->firstOrFail();

            $balance = $address->unconfirmed + $address->confirmed;

            if($paymentAddressAllocation->status == PaymentAddressAllocation::PAYMENT_STATUS_COMPLETED && $paymentAddressAllocation->block == 1) return true;

            if($address->confirmed >= $paymentAddressAllocation->amount && $paymentAddressAllocation->status == PaymentAddressAllocation::PAYMENT_STATUS_COMPLETED) {
                $paymentAddressAllocation->block = 1;
                $paymentAddressAllocation->save();
                $invoice->handleIpnCallbacks(); // Resend notifications
            }

            if($paymentAddressAllocation->status == PaymentAddressAllocation::PAYMENT_STATUS_COMPLETED) return true;

            if($balance == $paymentAddressAllocation->amount) { // correct payment
                $paymentAddressAllocation->status = PaymentAddressAllocation::PAYMENT_STATUS_COMPLETED;
                $paymentAddressAllocation->received = $paymentAddressAllocation->address->confirmed + $paymentAddressAllocation->address->unconfirmed;
                $paymentAddressAllocation->pending = $paymentAddressAllocation->amount - $paymentAddressAllocation->received;
                $paymentAddressAllocation->save();

                $invoice->setAsPaidByPayment($invoicePayment);
                return true;
            } elseif($balance != 0 && $balance < $paymentAddressAllocation->amount) { // underpayment / partial payment
                // Partial Payments should not get updated in case of restarts and if it is already a partial payment
                if($synchronize && $paymentAddressAllocation->status == PaymentAddressAllocation::PAYMENT_STATUS_PARTIAL_PAYMENT) {
                    return false;
                }

                $paymentAddressAllocation->status = PaymentAddressAllocation::PAYMENT_STATUS_PARTIAL_PAYMENT;
                $paymentAddressAllocation->received = $paymentAddressAllocation->address->confirmed + $paymentAddressAllocation->address->unconfirmed;
                $paymentAddressAllocation->pending = $paymentAddressAllocation->amount - $paymentAddressAllocation->received;
                // Extend Partial Payment Time
                $paymentAddressAllocation->expires_at = date("Y-m-d H:i:s", strtotime('+7 day'));
                $paymentAddressAllocation->save();

                $invoicePayment->electrum_expires_at = date("Y-m-d H:i:s", strtotime('+7 day'));
                $invoicePayment->save();

                $invoice->setAsPendingTxByPayment($invoicePayment);
                return false;
            } elseif($balance > $paymentAddressAllocation->amount) { // overpaid
                $paymentAddressAllocation->status = PaymentAddressAllocation::PAYMENT_STATUS_OVERPAID;
                $paymentAddressAllocation->received = $paymentAddressAllocation->address->confirmed + $paymentAddressAllocation->address->unconfirmed;
                $paymentAddressAllocation->pending = $paymentAddressAllocation->amount - $paymentAddressAllocation->received;
                $paymentAddressAllocation->save();

                $invoice->setAsOverpaidByPayment($invoicePayment);
                return true;
            }

        } catch (\Exception $e) {
            var_dump($e->getTraceAsString());
        }

        return false;
    }


    /**
     * @param $amount
     * @param $memo
     * @param string $expiration
     * @return PaymentAddressAllocation|bool
     */
    public function addRequest($amount, $memo, $expiration='+1day') {

        $address = $this->getAddressForPayment();

        if(!$address) return false;

        $paymentAddressAllocation = new PaymentAddressAllocation();
        $paymentAddressAllocation->wallet_id = $this->wallet->id;
        $paymentAddressAllocation->address_id = $address->id;
        $paymentAddressAllocation->amount = $amount;
        $paymentAddressAllocation->pending = $amount;
        $paymentAddressAllocation->received = 0;
        $paymentAddressAllocation->tx_cut_height = $address->height;
        $paymentAddressAllocation->memo = $memo;
        $paymentAddressAllocation->status = PaymentAddressAllocation::PAYMENT_STATUS_OPEN;
        $paymentAddressAllocation->expires_at = Carbon::parse($expiration)->format("Y-m-d H:i:s");

        try {
            $paymentAddressAllocation->save();

        } catch (\Exception $e) {
            var_dump($e->getTraceAsString());
            return false;
        }

        return $paymentAddressAllocation;
    }

    public function getAddressForPayment() {
        /** @var Address[] $addresses */
        $addresses = $this->wallet->addresses_by_chain(Wallet::HD_EXTERNAL_CHAIN)->get();

        $this->wallet->refresh();
        $height = $this->wallet->height;

        // HD Wallet has to be synchronized
        if(count($addresses) == 0 || count($addresses) < $this->wallet->gap_limit) return false;

        foreach($addresses as $address) {
            if($address->confirmed != 0) continue; // Use only addresses with 0 amount
            if($address->unconfirmed != 0) continue; // Use only addresses with 0 amount
            if($height - $address->height <= 20 && $height != 0) continue; // Last TX of addr must be at least 20 blocks old and only if we already got current height
            if($address->history >= 20) continue; // Limit TXes per Address to 20

            foreach($address->allocations()->get() as $allocation) {
                if(Carbon::parse($allocation->expires_at)->greaterThan(Carbon::now())) continue 2; // Not expired allocation for address
            }

            return $address;
        }

        // Force new address
        $new_address = $this->walletService->create_new_address(Wallet::HD_EXTERNAL_CHAIN);

        if($new_address) {
            $this->walletService->updateAddresses([$new_address]);
            return $new_address;
        }

        return false;
    }
}

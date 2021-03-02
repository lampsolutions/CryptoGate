<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentAddressAllocation extends Migration {
    public function up() {
        Schema::create('payment_address_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('wallet_id')->unsigned()->index();
            $table->integer('address_id')->unsigned()->index();
            $table->integer('amount')->unsigned();
            $table->integer('tx_cut_height')->unsigned()->default(0)->index();
            $table->longText( 'memo')->nullable();
            $table->string( 'status', 64)->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('payment_address_allocations');
    }
}

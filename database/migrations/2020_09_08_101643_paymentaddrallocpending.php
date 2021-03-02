<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Paymentaddrallocpending extends Migration {
    public function up() {
        Schema::table('payment_address_allocations', function (Blueprint $table) {
            $table->bigInteger('pending')->default(0); // pending can be negative
            $table->bigInteger('received')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('payment_address_allocations', function (Blueprint $table) {
            $table->dropColumn(['pending', 'received']);
        });
    }
}

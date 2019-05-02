<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->string('memo')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('status', ['Open','Paid','Expired'])->default('Open');
            $table->string('return_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->decimal('amount', 30, 8);
            $table->enum('currency', ['DASH','BTC','LTC','BCH']);
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->uuid('uuid')->unique();
            $table->enum('currency', ['DASH','BTC','LTC','BCH']);
            $table->decimal('electrum_amount', 30, 8);
            $table->string('electrum_id');
            $table->string('electrum_uri');
            $table->string('electrum_address');
            $table->dateTime('electrum_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_currencies');
        Schema::dropIfExists('invoice_payments');
    }
}

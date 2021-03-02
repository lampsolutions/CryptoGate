<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoinBtx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('invoice_payments_new');
        Schema::create('invoice_payments_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->uuid('uuid')->unique();
            $table->string('currency');
            $table->decimal('electrum_amount', 30, 8);
            $table->string('electrum_id');
            $table->string('electrum_uri');
            $table->string('electrum_address');
            $table->dateTime('electrum_expires_at')->nullable();
            $table->timestamps();
        });

        DB::statement('INSERT INTO invoice_payments_new SELECT id, invoice_id, uuid, currency, electrum_amount, electrum_id, electrum_uri, electrum_address, electrum_expires_at, created_at, updated_at FROM invoice_payments;');
        DB::statement('DROP TABLE invoice_payments;');
        DB::statement('ALTER TABLE invoice_payments_new RENAME TO invoice_payments;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

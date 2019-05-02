<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->uuid('uuid')->unique();
            $table->string('memo')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('return_url')->nullable();
            $table->string('callback_url')->nullable();

            $table->decimal('amount', 30, 2);
            $table->string('currency')->default('EUR');

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
        Schema::drop('invoice_requests');
    }
}

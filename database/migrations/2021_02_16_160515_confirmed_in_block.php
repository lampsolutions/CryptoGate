<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConfirmedInBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('ipn_url', 2048)->nullable();
        });

        Schema::table('payment_address_allocations', function (Blueprint $table) {
            $table->integer('block')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_address_allocations', function (Blueprint $table) {
            $table->dropColumn(['block']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['ipn_url']);
        });
    }
}

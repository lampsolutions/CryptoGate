<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Address extends Migration {
    public function up() {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address', 64)->index();
            $table->string('script_hash', 64)->index();
            $table->integer('hd_index')->unsigned()->index();
            $table->integer('hd_chain')->unsigned()->index();
            $table->integer('history')->unsigned()->index();
            $table->integer('height')->unsigned()->index();
            $table->bigInteger('confirmed');
            $table->bigInteger('unconfirmed');
            $table->integer('wallet_id')->unsigned()->index();

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('addresses');
    }
}

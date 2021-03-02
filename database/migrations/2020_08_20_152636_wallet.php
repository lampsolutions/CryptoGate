<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Wallet extends Migration {

    public function up() {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('public_key', 128)->unique();
            $table->string('coin', 32);
            $table->string('network', 32);
            $table->integer('height')->default(0);
            $table->string('server_dsn', 128);
            $table->string('gap_limit')->default(100);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('wallets');
    }
}

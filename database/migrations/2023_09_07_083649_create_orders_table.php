<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('method')->nullable();
            $table->string('order_number')->nullable();
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();

            $table->string('txid')->nullable();
            $table->string('conversion_rate')->nullable();
            $table->string('status')->default(0); //0 processing, 1 complete, 2 failed
            $table->string('autoreceive')->default(0); //0 processing, 1 trx send, 2 complete, 3 fail
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
        Schema::dropIfExists('orders');
    }
};

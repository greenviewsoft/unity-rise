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
        Schema::create('addresstrxes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('address_hex');
            $table->text('address_base58');
            $table->text('private_key');
            $table->text('public_key');
            $table->string('is_validate');
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();
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
        Schema::dropIfExists('addresstrxes');
    }
};

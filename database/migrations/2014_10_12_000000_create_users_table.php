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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('pshow')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('invitation_code')->nullable();
            $table->string('phone')->nullable();
            $table->integer('refer_id')->nullable();

            $table->integer('refer_code')->nullable();
            $table->string('image')->nullable();
            $table->text('information')->nullable();

            $table->decimal('balance', 13, 5)->default(00.00)->nullable();
            $table->decimal('demo_balance', 13, 2)->default(33000.00)->nullable();
            $table->decimal('refer_commission', 13, 2)->default(00.00)->nullable();
            $table->integer('rank')->default(1);

            $table->string('crypto_type')->nullable();
            $table->string('crypto_address')->nullable();
            $table->string('crypto_password')->nullable();
            $table->string('status')->default('on');
            $table->string('wallet_address')->nullable();
            $table->text('wallet_private_key')->nullable();
            $table->string('currency')->default('trx');

            $table->integer('withdraw_check')->default('0');
            $table->integer('spin')->default('0');

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};

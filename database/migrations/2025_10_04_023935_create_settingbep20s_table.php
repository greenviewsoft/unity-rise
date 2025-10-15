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
       Schema::create('settingbep20s', function (Blueprint $table) {
    $table->id();

    // Receiver settings
    $table->boolean('receiver_status')->default(true);
    $table->text('receiver_address')->nullable();
    $table->text('receiver_private_key')->nullable();

    // Gas and network settings
    $table->integer('gas_limit')->default(21000);

    // Sender settings
    $table->boolean('sender_status')->default(true);
    $table->text('sender_address')->nullable();
    $table->text('sender_private_key')->nullable();

    // Financial settings
    $table->decimal('usdt_to_usd_rate', 13, 5)->default(1.0);
    $table->integer('min_withdraw')->default(10);
    $table->integer('withdraw_fee')->default(2);

    // Network configuration
    $table->string('network_name')->default('BSC');
    $table->string('contract_address')->default('0x55d398326f99059fF775485246999027B3197955'); // USDT BEP20
    $table->text('rpc_url')->nullable();

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
        Schema::dropIfExists('settingbep20s');
    }
};

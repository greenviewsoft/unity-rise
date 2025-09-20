<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settingtrxes', function (Blueprint $table) {
            $table->id();

            $table->string('receiver_status', 10)->default('1'); // text -> string
            $table->text('receiver_address')->nullable();
            $table->text('receiver_privatekey')->nullable();
            $table->integer('energy')->default(20);

            $table->string('sender_status', 10)->default('1'); // text -> string
            $table->text('sender_address')->nullable();
            $table->text('sender_privatekey')->nullable();

            $table->decimal('conversion', 13, 5)->default(0.079);
            $table->integer('min_withdraw')->default(49);
            $table->integer('withdraw_vat')->default(2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settingtrxes');
    }
};

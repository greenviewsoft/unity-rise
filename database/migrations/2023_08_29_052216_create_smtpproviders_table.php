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
        Schema::create('smtpproviders', function (Blueprint $table) {
            $table->id();
            $table->string('smtp_name');
            $table->string('hostname');
            $table->string('username');
            $table->string('password');
            $table->string('port');
            $table->string('connection');
            $table->string('reply_to');
            $table->string('from_email');
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
        Schema::dropIfExists('smtpproviders');
    }
};

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
        Schema::create('refercommissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('level');
            $table->decimal('deposite_amount', 13, 5)->default(0.00000);
            $table->decimal('commission', 13, 5)->default(0.00000);
            $table->integer('refuser');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('refercommissions');
    }
};
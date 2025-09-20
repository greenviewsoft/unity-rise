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
        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('rank')->unique();
            $table->decimal('team_business_volume', 15, 2)->default(0);
            $table->integer('count_level')->default(0);
            $table->decimal('personal_investment', 15, 2)->default(0);
            $table->decimal('reward_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('rank_requirements');
    }
};

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
        Schema::create('referral_commission_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('rank_id')->comment('Rank ID (1-12)');
            $table->string('rank_name', 50)->comment('Rank name (Bronze, Silver, etc.)');
            $table->integer('level')->comment('Level number within rank (1-40)');
            $table->decimal('commission_rate', 8, 4)->comment('Commission rate percentage');
            $table->decimal('rank_reward', 10, 2)->default(0)->comment('Reward for achieving this rank');
            $table->integer('max_levels')->comment('Maximum levels for this rank');
            $table->boolean('is_active')->default(true)->comment('Whether this level is active');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['rank_id', 'level']);
            $table->index('rank_name');
            $table->unique(['rank_id', 'level'], 'unique_rank_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_commission_levels');
    }
};

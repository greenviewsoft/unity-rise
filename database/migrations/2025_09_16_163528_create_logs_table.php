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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // rank_reward, investment_profit, deposit, withdrawal, balance_update, etc.
            $table->decimal('amount', 15, 2)->nullable(); // Transaction amount
            $table->decimal('previous_balance', 15, 2)->nullable(); // Balance before transaction
            $table->decimal('new_balance', 15, 2)->nullable(); // Balance after transaction
            $table->string('reference_type')->nullable(); // Model type (Investment, RankReward, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Related model ID
            $table->text('description')->nullable(); // Transaction description
            $table->json('metadata')->nullable(); // Additional data (old_rank, new_rank, etc.)
            $table->string('status')->default('completed'); // completed, pending, failed
            $table->string('ip_address')->nullable(); // User IP for security
            $table->string('user_agent')->nullable(); // User agent for security
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'transaction_type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};

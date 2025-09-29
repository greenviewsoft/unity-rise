<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('plan_type')->default('starter');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->decimal('daily_profit', 8, 2);
            $table->decimal('total_profit', 10, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('profit_days_completed')->default(0);
            $table->date('last_profit_date')->nullable();
            $table->timestamps();
            
            $table->foreign('plan_id')->references('id')->on('investment_plans')->onDelete('set null');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_profit_date']);
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};

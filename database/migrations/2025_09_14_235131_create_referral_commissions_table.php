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
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->decimal('investment_amount', 10, 2);
            $table->decimal('commission_amount', 8, 2);
            $table->integer('level')->comment('Referral level (1-40)');
            $table->integer('rank')->comment('Referrer rank (1-12)');
            $table->datetime('commission_date');
            $table->timestamps();
            
            $table->index(['referrer_id', 'commission_date']);
            $table->index(['referred_id', 'commission_date']);
            $table->index(['level', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
    }
};

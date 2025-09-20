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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_com', 8, 2)->default(0.00);
            $table->decimal('bonus', 8, 2)->default(0.00);
            
            // Commission rates for levels 1-40
            for ($i = 1; $i <= 40; $i++) {
                $table->decimal('refer_com' . $i, 8, 4)->default(0.0000);
            }
            
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
        Schema::dropIfExists('commissions');
    }
};